<?php

declare(strict_types=1);

namespace Atoolo\Resource\Controller;

use Atoolo\Resource\Change\ResourceChangeMessage;
use Atoolo\Resource\Change\ResourceChanges;
use InvalidArgumentException;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Receives the resources the CMS has created, changed or removed.
 *
 * The path lies below `/api/admin/`, whose access is restricted to
 * `ROLE_ADMIN` and `ROLE_API` by the security configuration of the
 * website. The CMS sends the notification to the host of the channel, and
 * every host is mapped to exactly one channel, so the notification belongs
 * to the channel of this request. It is only handed to the message bus
 * here, which stores it in the spool of that channel (see
 * {@see \Atoolo\Resource\Messenger\ChannelTransport}); the worker of the
 * channel handles it later, so the CMS never waits for the handlers.
 */
#[AsController]
final class ResourceChangeController
{
    /**
     * Version of the notification format. The CMS asks for it to learn
     * whether a website accepts notifications at all.
     */
    public const VERSION = 1;

    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {}

    #[Route(
        '/api/admin/resource/changes',
        name: 'atoolo_resource_changes_version',
        methods: ['GET'],
    )]
    public function version(): Response
    {
        return new JsonResponse(['version' => self::VERSION]);
    }

    #[Route(
        '/api/admin/resource/changes',
        name: 'atoolo_resource_changes_notify',
        methods: ['POST'],
    )]
    public function notify(Request $request): Response
    {
        try {
            $changes = ResourceChanges::fromArray(json_decode(
                $request->getContent(),
                true,
                16,
                JSON_THROW_ON_ERROR,
            ));
        } catch (JsonException|InvalidArgumentException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST,
            );
        }

        if (!$changes->isEmpty()) {
            $this->bus->dispatch(new ResourceChangeMessage($changes));
        }

        return new JsonResponse(
            ['accepted' => $changes->count()],
            Response::HTTP_ACCEPTED,
        );
    }
}
