<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Controller;

use Atoolo\Resource\Change\ResourceChange;
use Atoolo\Resource\Change\ResourceChangeMessage;
use Atoolo\Resource\Change\ResourceChanges;
use Atoolo\Resource\Controller\ResourceChangeController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

#[CoversClass(ResourceChangeController::class)]
class ResourceChangeControllerTest extends TestCase
{
    public function testVersion(): void
    {
        $controller = new ResourceChangeController(
            $this->createStub(MessageBusInterface::class),
        );
        $response = $controller->version();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('{"version":1}', $response->getContent());
    }

    public function testNotifyDispatchesTheChanges(): void
    {
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())
            ->method('dispatch')
            ->with(
                new ResourceChangeMessage(
                    new ResourceChanges([new ResourceChange('1', '/a.php')], ['2']),
                ),
            )
            ->willReturnCallback(static fn(object $m) => new Envelope($m));

        $response = (new ResourceChangeController($bus))->notify($this->request(
            '{"changed":[{"id":"1","path":"/a.php"}],"removed":[{"id":"2"}]}',
        ));

        $this->assertSame(Response::HTTP_ACCEPTED, $response->getStatusCode());
        $this->assertSame('{"accepted":2}', $response->getContent());
    }

    public function testEmptyNotificationIsNotDispatched(): void
    {
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->never())->method('dispatch');

        $response = (new ResourceChangeController($bus))->notify(
            $this->request('{}'),
        );
        $this->assertSame(Response::HTTP_ACCEPTED, $response->getStatusCode());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidBodies(): array
    {
        return [
            'no json' => ['{'],
            'no object' => ['"x"'],
            'invalid change' => ['{"changed":[{"id":"1"}]}'],
        ];
    }

    #[DataProvider('invalidBodies')]
    public function testInvalidNotification(string $body): void
    {
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->never())->method('dispatch');

        $response = (new ResourceChangeController($bus))->notify(
            $this->request($body),
        );
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    private function request(string $body): Request
    {
        return Request::create(
            '/api/admin/resource/changes',
            'POST',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: $body,
        );
    }
}
