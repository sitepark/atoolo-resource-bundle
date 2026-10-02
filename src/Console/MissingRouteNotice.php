<?php

declare(strict_types=1);

namespace Atoolo\Resource\Console;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Routing\RouterInterface;
use Throwable;

/**
 * Points out a missing route of the resource change notification after
 * `cache:clear`.
 *
 * Symfony Flex applies a recipe only when a package is installed, never
 * when it is updated. A project that installed the bundle before it had a
 * recipe therefore lacks the route import, and the CMS cannot notify the
 * website. Every `composer install` and `update` of a project ends with
 * `cache:clear`, so this is where the command to install the recipe is
 * shown.
 */
#[AsEventListener(ConsoleEvents::TERMINATE)]
final class MissingRouteNotice
{
    public const ROUTE = 'atoolo_resource_changes_notify';

    public const COMMAND
        = 'composer recipes:install atoolo/resource-bundle --force -v';

    public function __construct(
        private readonly ?RouterInterface $router = null,
    ) {}

    public function __invoke(ConsoleTerminateEvent $event): void
    {
        if (
            $this->router === null
            || $event->getExitCode() !== 0
            || $event->getCommand()?->getName() !== 'cache:clear'
        ) {
            return;
        }

        try {
            if ($this->router->getRouteCollection()->get(self::ROUTE) !== null) {
                return;
            }
        } catch (Throwable) {
            // the notice must never let cache:clear fail
            return;
        }

        (new SymfonyStyle($event->getInput(), $event->getOutput()))->warning([
            'atoolo/resource-bundle: the route of the resource change'
            . ' notification is missing, so the CMS cannot notify this'
            . ' website about published resources. Install the recipe:',
            self::COMMAND,
        ]);
    }
}
