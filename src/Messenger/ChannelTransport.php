<?php

declare(strict_types=1);

namespace Atoolo\Resource\Messenger;

use Closure;
use JsonException;
use RuntimeException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * Keeps messages as files, one directory per channel.
 *
 * The websites have no message broker, only a worker per channel. A
 * message is stored in `<directory>/<anchor>/<queue>/`: the anchor is the
 * one of the {@see ChannelStamp}, or the one of the channel of the sending
 * process. A worker reads only the directory of its own channel, which it
 * knows from the path it is called by - `…/www/app/bin/console` is the
 * channel `www`.
 *
 * A file is written under a temporary name and renamed, so a worker never
 * reads a half written message. A worker claims a message by renaming it
 * to `*.processing`, so several workers of one channel never handle the
 * same message. A claim older than {@see STALE_AFTER} seconds belongs to a
 * worker that died and is released again.
 */
final class ChannelTransport implements
    TransportInterface,
    MessageCountAwareInterface
{
    public const STALE_AFTER = 3600;

    private const SUFFIX = '.msg';

    private const PROCESSING = '.processing';

    private ?string $ownAnchor = null;

    /**
     * @param Closure(): string $anchor the anchor of the channel of this
     *   process, resolved on first use
     * @param Closure(): int $clock the current time in milliseconds
     */
    public function __construct(
        private readonly string $directory,
        private readonly string $queue,
        private readonly Closure $anchor,
        private readonly SerializerInterface $serializer,
        private readonly ?Closure $clock = null,
    ) {}

    public function send(Envelope $envelope): Envelope
    {
        $anchor = $envelope->last(ChannelStamp::class)->anchor
            ?? $this->ownAnchor();
        $directory = $this->queueDirectory($anchor);
        $this->ensureDirectory($directory);

        $delay = $envelope->last(DelayStamp::class)?->getDelay() ?? 0;
        $name = sprintf(
            '%015d-%s',
            $this->now() + max(0, $delay),
            bin2hex(random_bytes(8)),
        );

        $encoded = $this->serializer->encode(
            $envelope->withoutAll(TransportMessageIdStamp::class),
        );
        try {
            $content = json_encode($encoded, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }

        $tmp = $directory . '/.' . $name . '.tmp';
        if (@file_put_contents($tmp, $content) === false) {
            throw new TransportException('Unable to write ' . $tmp);
        }
        $file = $directory . '/' . $name . self::SUFFIX;
        if (!@rename($tmp, $file)) {
            @unlink($tmp);
            throw new TransportException('Unable to write ' . $file);
        }

        return $envelope->with(new TransportMessageIdStamp($file));
    }

    /**
     * @return iterable<Envelope>
     */
    public function get(): iterable
    {
        $directory = $this->queueDirectory($this->ownAnchor());
        if (!is_dir($directory)) {
            return [];
        }
        $this->releaseStaleClaims($directory);

        $now = $this->now();
        foreach ($this->pending($directory) as $file) {
            if ($this->dueAt($file) > $now) {
                break;
            }
            $claimed = $file . self::PROCESSING;
            if (!@rename($file, $claimed)) {
                // another worker was faster
                continue;
            }
            return [$this->decode($claimed)];
        }
        return [];
    }

    public function ack(Envelope $envelope): void
    {
        $this->remove($envelope);
    }

    public function reject(Envelope $envelope): void
    {
        $this->remove($envelope);
    }

    public function getMessageCount(): int
    {
        $directory = $this->queueDirectory($this->ownAnchor());
        if (!is_dir($directory)) {
            return 0;
        }
        return count($this->pending($directory));
    }

    private function decode(string $file): Envelope
    {
        $content = @file_get_contents($file);
        try {
            if ($content === false) {
                throw new RuntimeException('Unable to read ' . $file);
            }
            /** @var array{body: string, headers?: array<string>} $encoded */
            $encoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            $envelope = $this->serializer->decode($encoded);
        } catch (JsonException|RuntimeException|MessageDecodingFailedException $e) {
            $this->moveToBroken($file);
            throw new MessageDecodingFailedException(
                'Unable to decode ' . $file . ': ' . $e->getMessage(),
                0,
                $e,
            );
        }
        return $envelope->with(new TransportMessageIdStamp($file));
    }

    private function remove(Envelope $envelope): void
    {
        $file = $envelope->last(TransportMessageIdStamp::class)?->getId();
        if (is_string($file) && str_ends_with($file, self::PROCESSING)) {
            @unlink($file);
        }
    }

    /**
     * @return list<string> the pending messages, the oldest first
     */
    private function pending(string $directory): array
    {
        $files = glob($directory . '/*' . self::SUFFIX) ?: [];
        sort($files);
        return $files;
    }

    private function releaseStaleClaims(string $directory): void
    {
        $limit = time() - self::STALE_AFTER;
        foreach (glob($directory . '/*' . self::PROCESSING) ?: [] as $claimed) {
            $mtime = @filemtime($claimed);
            if ($mtime !== false && $mtime < $limit) {
                @rename($claimed, substr($claimed, 0, -strlen(self::PROCESSING)));
            }
        }
    }

    private function moveToBroken(string $file): void
    {
        $broken = dirname($file) . '/broken';
        $this->ensureDirectory($broken);
        @rename($file, $broken . '/' . basename($file));
    }

    private function dueAt(string $file): int
    {
        return (int) substr(basename($file), 0, 15);
    }

    private function queueDirectory(string $anchor): string
    {
        ChannelStamp::validate($anchor);
        return $this->directory . '/' . $anchor . '/' . $this->queue;
    }

    private function ownAnchor(): string
    {
        return $this->ownAnchor ??= ($this->anchor)();
    }

    private function now(): int
    {
        return $this->clock !== null
            ? ($this->clock)()
            : (int) floor(microtime(true) * 1000);
    }

    private function ensureDirectory(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }
        if (!@mkdir($directory, 0o775, true) && !is_dir($directory)) {
            throw new TransportException(
                'Unable to create directory ' . $directory,
            );
        }
    }
}
