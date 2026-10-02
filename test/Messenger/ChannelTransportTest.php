<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Messenger;

use Atoolo\Resource\Messenger\ChannelStamp;
use Atoolo\Resource\Messenger\ChannelTransport;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

#[CoversClass(ChannelTransport::class)]
class ChannelTransportTest extends TestCase
{
    private string $directory;

    private int $now = 1_000_000;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir()
            . '/atoolo-channel-transport-' . uniqid();
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
    }

    public function testSendAndGetInOwnChannel(): void
    {
        $transport = $this->transport('www');
        $transport->send(new Envelope($this->message('a')));

        $envelopes = [...$transport->get()];

        $this->assertCount(1, $envelopes);
        $message = $envelopes[0]->getMessage();
        $this->assertInstanceOf(stdClass::class, $message);
        $this->assertSame('a', $message->name);
        $this->assertNotNull($envelopes[0]->last(TransportMessageIdStamp::class));
    }

    public function testStampChoosesTheChannel(): void
    {
        $www = $this->transport('www');
        $preview = $this->transport('preview');

        $www->send(new Envelope($this->message('a'), [new ChannelStamp('preview')]));

        $this->assertSame([], [...$www->get()]);
        $this->assertCount(1, [...$preview->get()]);
        $this->assertDirectoryExists($this->directory . '/preview/default');
    }

    public function testMessagesComeInOrder(): void
    {
        $transport = $this->transport('www');
        $transport->send(new Envelope($this->message('a')));
        $this->now++;
        $transport->send(new Envelope($this->message('b')));

        $first = [...$transport->get()][0];
        $second = [...$transport->get()][0];

        $this->assertSame('a', $this->nameOf($first));
        $this->assertSame('b', $this->nameOf($second));
        $this->assertSame([], [...$transport->get()], 'both are claimed');
    }

    public function testDelayedMessageIsNotDueYet(): void
    {
        $transport = $this->transport('www');
        $transport->send(new Envelope($this->message('a'), [new DelayStamp(5000)]));

        $this->assertSame([], [...$transport->get()]);
        $this->assertSame(1, $transport->getMessageCount());

        $this->now += 5000;
        $this->assertCount(1, [...$transport->get()]);
    }

    public function testAckRemovesTheMessage(): void
    {
        $transport = $this->transport('www');
        $transport->send(new Envelope($this->message('a')));
        $envelope = [...$transport->get()][0];

        $transport->ack($envelope);

        $this->assertSame([], glob($this->directory . '/www/default/*') ?: []);
    }

    public function testRejectRemovesTheMessage(): void
    {
        $transport = $this->transport('www');
        $transport->send(new Envelope($this->message('a')));
        $envelope = [...$transport->get()][0];

        $transport->reject($envelope);

        $this->assertSame([], glob($this->directory . '/www/default/*') ?: []);
    }

    public function testStaleClaimIsReleased(): void
    {
        $transport = $this->transport('www');
        $transport->send(new Envelope($this->message('a')));
        [...$transport->get()];

        $claimed = glob($this->directory . '/www/default/*.processing') ?: [];
        $this->assertCount(1, $claimed);
        touch($claimed[0], time() - ChannelTransport::STALE_AFTER - 1);

        $this->assertCount(1, [...$transport->get()]);
    }

    public function testBrokenMessageIsMovedAside(): void
    {
        $transport = $this->transport('www');
        mkdir($this->directory . '/www/default', 0o775, true);
        file_put_contents(
            $this->directory . '/www/default/000000000000001-x.msg',
            '{',
        );

        try {
            [...$transport->get()];
            $this->fail('exception expected');
        } catch (MessageDecodingFailedException) {
        }
        $this->assertCount(
            1,
            glob($this->directory . '/www/default/broken/*') ?: [],
        );
    }

    public function testInvalidAnchorIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->transport('../etc')->send(new Envelope($this->message('a')));
    }

    public function testMessageCountWithoutDirectory(): void
    {
        $this->assertSame(0, $this->transport('www')->getMessageCount());
    }

    private function transport(string $anchor): ChannelTransport
    {
        return new ChannelTransport(
            $this->directory,
            'default',
            static fn(): string => $anchor,
            new PhpSerializer(),
            fn(): int => $this->now,
        );
    }

    private function message(string $name): stdClass
    {
        $message = new stdClass();
        $message->name = $name;
        return $message;
    }

    private function nameOf(Envelope $envelope): string
    {
        $message = $envelope->getMessage();
        $this->assertInstanceOf(stdClass::class, $message);
        $this->assertIsString($message->name);
        return $message->name;
    }
}
