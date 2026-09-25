<?php

declare(strict_types=1);

namespace Atoolo\Resource\Change;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;
use Throwable;

/**
 * Hands the changes of a {@see ResourceChangeMessage} to every
 * {@see ResourceChangeHandler}.
 *
 * A failing or deferring handler does not keep the others from running.
 * Afterwards a failure lets the retry strategy of the transport repeat the
 * message; a deferral repeats it after {@see DEFERRAL_DELAY} milliseconds,
 * as often as it takes. Every handler sees a repeated message again.
 */
#[AsMessageHandler]
class ResourceChangeMessageHandler
{
    public const DEFERRAL_DELAY = 60_000;

    /**
     * @param iterable<ResourceChangeHandler> $handlers
     */
    public function __construct(
        private readonly iterable $handlers,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function __invoke(ResourceChangeMessage $message): void
    {
        $changes = $message->changes;
        $this->logger->info('Handle resource changes', [
            'changed' => count($changes->changed),
            'removed' => count($changes->removedIds),
        ]);

        $failed = [];
        $deferred = [];
        foreach ($this->handlers as $handler) {
            try {
                $handler->handle($changes);
            } catch (ResourceChangeDeferredException $e) {
                $deferred[] = $handler::class . ' (' . $e->getMessage() . ')';
            } catch (Throwable $e) {
                $this->logger->error('Resource change handler failed', [
                    'handler' => $handler::class,
                    'exception' => $e,
                ]);
                $failed[] = $handler::class;
            }
        }

        if (!empty($failed)) {
            throw new RuntimeException(
                'Resource change handler failed: ' . implode(', ', $failed),
            );
        }
        if (!empty($deferred)) {
            $this->logger->info(
                'Resource changes deferred: ' . implode(', ', $deferred),
            );
            throw new RecoverableMessageHandlingException(
                'Resource changes deferred: ' . implode(', ', $deferred),
                0,
                null,
                self::DEFERRAL_DELAY,
            );
        }
    }
}
