<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Console;

use Atoolo\Resource\Console\MissingRouteNotice;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

#[CoversClass(MissingRouteNotice::class)]
class MissingRouteNoticeTest extends TestCase
{
    public function testWarnsIfRouteIsMissing(): void
    {
        $output = $this->runNotice(new MissingRouteNotice($this->router(false)));

        $this->assertStringContainsString(
            MissingRouteNotice::COMMAND,
            $output,
        );
    }

    public function testSilentIfRouteExists(): void
    {
        $output = $this->runNotice(new MissingRouteNotice($this->router(true)));
        $this->assertSame('', $output);
    }

    public function testSilentForOtherCommands(): void
    {
        $output = $this->runNotice(
            new MissingRouteNotice($this->router(false)),
            'cache:warmup',
        );
        $this->assertSame('', $output);
    }

    public function testSilentIfCacheClearFailed(): void
    {
        $output = $this->runNotice(
            new MissingRouteNotice($this->router(false)),
            'cache:clear',
            1,
        );
        $this->assertSame('', $output);
    }

    public function testSilentWithoutRouter(): void
    {
        $output = $this->runNotice(new MissingRouteNotice());
        $this->assertSame('', $output);
    }

    public function testSilentIfRoutesCannotBeLoaded(): void
    {
        $router = $this->createStub(RouterInterface::class);
        $router->method('getRouteCollection')
            ->willThrowException(new RuntimeException('broken'));

        $output = $this->runNotice(new MissingRouteNotice($router));
        $this->assertSame('', $output);
    }

    private function runNotice(
        MissingRouteNotice $notice,
        string $command = 'cache:clear',
        int $exitCode = 0,
    ): string {
        $output = new BufferedOutput();
        $notice(new ConsoleTerminateEvent(
            new Command($command),
            new ArrayInput([]),
            $output,
            $exitCode,
        ));
        return $output->fetch();
    }

    private function router(bool $withRoute): RouterInterface
    {
        $routes = new RouteCollection();
        if ($withRoute) {
            $routes->add(
                MissingRouteNotice::ROUTE,
                new Route('/api/admin/resource/changes'),
            );
        }
        $router = $this->createStub(RouterInterface::class);
        $router->method('getRouteCollection')->willReturn($routes);
        return $router;
    }
}
