<?php

declare(strict_types=1);

namespace Atoolo\Resource\Messenger;

use InvalidArgumentException;
use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * Names the channel a message belongs to, by the anchor of the channel.
 *
 * The {@see ChannelTransport} stores the message in the spool of that
 * channel, where only the worker of the channel picks it up. Without the
 * stamp the message belongs to the channel of the process sending it.
 */
final class ChannelStamp implements StampInterface
{
    public const PATTERN = '/^[A-Za-z0-9._-]{1,64}$/';

    public function __construct(
        public readonly string $anchor,
    ) {
        self::validate($anchor);
    }

    /**
     * An anchor becomes a directory name, so it is restricted to letters,
     * digits, `.`, `_` and `-`.
     *
     * @throws InvalidArgumentException
     */
    public static function validate(string $anchor): void
    {
        if (
            preg_match(self::PATTERN, $anchor) !== 1
            || $anchor === '.'
            || $anchor === '..'
        ) {
            throw new InvalidArgumentException(
                'invalid channel anchor: ' . $anchor,
            );
        }
    }
}
