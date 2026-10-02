<?php

declare(strict_types=1);

namespace Atoolo\Resource\Messenger;

use Atoolo\Resource\Factory\ResourceChannelFactory;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * Creates the {@see ChannelTransport} of a DSN `atoolo-channel://<queue>`.
 *
 * @implements TransportFactoryInterface<ChannelTransport>
 */
#[AutoconfigureTag('messenger.transport_factory')]
final class ChannelTransportFactory implements TransportFactoryInterface
{
    public const SCHEME = 'atoolo-channel://';

    public function __construct(
        private readonly string $directory,
        private readonly ResourceChannelFactory $channelFactory,
    ) {}

    /**
     * @param array<mixed> $options
     */
    public function createTransport(
        #[\SensitiveParameter]
        string $dsn,
        array $options,
        SerializerInterface $serializer,
    ): TransportInterface {
        $queue = substr($dsn, strlen(self::SCHEME));
        $queue = $queue === '' ? 'default' : $queue;
        if (preg_match('/^[A-Za-z0-9_-]{1,64}$/', $queue) !== 1) {
            throw new InvalidArgumentException(
                'invalid queue in ' . $dsn,
            );
        }
        return new ChannelTransport(
            $this->directory,
            $queue,
            fn(): string => $this->channelFactory->create()->anchor,
            $serializer,
        );
    }

    /**
     * @param array<mixed> $options
     */
    public function supports(
        #[\SensitiveParameter]
        string $dsn,
        array $options,
    ): bool {
        return str_starts_with($dsn, self::SCHEME);
    }
}
