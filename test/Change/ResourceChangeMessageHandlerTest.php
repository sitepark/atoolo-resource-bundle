<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Change;

use Atoolo\Resource\Change\ResourceChange;
use Atoolo\Resource\Change\ResourceChangeDeferredException;
use Atoolo\Resource\Change\ResourceChangeHandler;
use Atoolo\Resource\Change\ResourceChangeMessage;
use Atoolo\Resource\Change\ResourceChangeMessageHandler;
use Atoolo\Resource\Change\ResourceChanges;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;

#[CoversClass(ResourceChangeMessageHandler::class)]
class ResourceChangeMessageHandlerTest extends TestCase
{
    private ResourceChangeMessage $message;

    protected function setUp(): void
    {
        $this->message = new ResourceChangeMessage(
            new ResourceChanges([new ResourceChange('1', '/a.php')]),
        );
    }

    public function testCallsAllHandlers(): void
    {
        $a = $this->createMock(ResourceChangeHandler::class);
        $a->expects($this->once())->method('handle')->with($this->message->changes);
        $b = $this->createMock(ResourceChangeHandler::class);
        $b->expects($this->once())->method('handle')->with($this->message->changes);

        (new ResourceChangeMessageHandler([$a, $b]))($this->message);
    }

    public function testFailingHandlerKeepsOthersAndFails(): void
    {
        $failing = $this->createStub(ResourceChangeHandler::class);
        $failing->method('handle')->willThrowException(new RuntimeException('x'));
        $other = $this->createMock(ResourceChangeHandler::class);
        $other->expects($this->once())->method('handle');

        $this->expectException(RuntimeException::class);
        (new ResourceChangeMessageHandler([$failing, $other]))($this->message);
    }

    public function testDeferringHandlerRepeatsTheMessage(): void
    {
        $deferring = $this->createStub(ResourceChangeHandler::class);
        $deferring->method('handle')
            ->willThrowException(new ResourceChangeDeferredException('running'));

        try {
            (new ResourceChangeMessageHandler([$deferring]))($this->message);
            $this->fail('exception expected');
        } catch (RecoverableMessageHandlingException $e) {
            $this->assertSame(
                ResourceChangeMessageHandler::DEFERRAL_DELAY,
                $e->getRetryDelay(),
            );
        }
    }

    public function testFailureWinsOverDeferral(): void
    {
        $deferring = $this->createStub(ResourceChangeHandler::class);
        $deferring->method('handle')
            ->willThrowException(new ResourceChangeDeferredException('running'));
        $failing = $this->createStub(ResourceChangeHandler::class);
        $failing->method('handle')->willThrowException(new RuntimeException('x'));

        try {
            (new ResourceChangeMessageHandler([$deferring, $failing]))($this->message);
            $this->fail('exception expected');
        } catch (RecoverableMessageHandlingException) {
            $this->fail('a failure must count as an attempt');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('failed', $e->getMessage());
        }
    }
}
