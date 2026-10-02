<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Messenger;

use Atoolo\Resource\Factory\ResourceChannelFactory;
use Atoolo\Resource\Messenger\ChannelStamp;
use Atoolo\Resource\Messenger\ChannelTransport;
use Atoolo\Resource\Messenger\ChannelTransportFactory;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

#[CoversClass(ChannelTransportFactory::class)]
#[CoversClass(ChannelStamp::class)]
class ChannelTransportFactoryTest extends TestCase
{
    public function testSupports(): void
    {
        $factory = $this->factory();
        $this->assertTrue($factory->supports('atoolo-channel://default', []));
        $this->assertFalse($factory->supports('doctrine://default', []));
    }

    public function testCreateTransport(): void
    {
        $transport = $this->factory()->createTransport(
            'atoolo-channel://failed',
            [],
            new PhpSerializer(),
        );
        $this->assertInstanceOf(ChannelTransport::class, $transport);
    }

    public function testInvalidQueue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory()->createTransport(
            'atoolo-channel://../x',
            [],
            new PhpSerializer(),
        );
    }

    public function testValidAnchor(): void
    {
        $this->assertSame('www-stuttgart_1.0', (new ChannelStamp('www-stuttgart_1.0'))->anchor);
    }

    public function testDotsAreNoAnchor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ChannelStamp('..');
    }

    private function factory(): ChannelTransportFactory
    {
        return new ChannelTransportFactory(
            '/tmp/spool',
            $this->createStub(ResourceChannelFactory::class),
        );
    }
}
