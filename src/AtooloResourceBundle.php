<?php

declare(strict_types=1);

namespace Atoolo\Resource;

use Atoolo\Resource\Change\ResourceChangeHandler;
use Atoolo\Resource\Change\ResourceChangeMessage;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @codeCoverageIgnore
 */
class AtooloResourceBundle extends AbstractBundle
{
    /**
     * The transport through which atoolo bundles hand messages to the
     * worker of a channel, see {@see Messenger\ChannelTransport}.
     */
    public const CHANNEL_TRANSPORT = 'atoolo_channel';

    /**
     * Keeps `@AtooloResourceBundle/` pointing to `src/`, as it did before
     * the bundle became an `AbstractBundle` - the route import of the
     * recipe is `@AtooloResourceBundle/Controller/`.
     */
    public function getPath(): string
    {
        return __DIR__;
    }

    public function build(ContainerBuilder $container): void
    {
        $configDir = __DIR__ . '/../config';

        $locator = new FileLocator($configDir);
        $loader = new GlobFileLoader($locator);
        $loader->setResolver(
            new LoaderResolver(
                [
                    new YamlFileLoader($container, $locator),
                ],
            ),
        );

        $loader->load('services.yaml');

        // a handler of any bundle is tagged, as long as it is autoconfigured
        $container->registerForAutoconfiguration(ResourceChangeHandler::class)
            ->addTag(ResourceChangeHandler::TAG);
    }

    /**
     * Registers the channel transport, so that a project needs no
     * messenger configuration of its own. Another bundle routes its
     * messages to {@see CHANNEL_TRANSPORT} the same way.
     */
    public function prependExtension(
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        if (!$builder->hasExtension('framework')) {
            return;
        }
        $builder->prependExtensionConfig('framework', [
            'messenger' => [
                'transports' => [
                    self::CHANNEL_TRANSPORT => [
                        'dsn' => 'atoolo-channel://default',
                        'retry_strategy' => [
                            'max_retries' => 5,
                            'delay' => 10000,
                            'multiplier' => 2,
                        ],
                        'failure_transport' => self::CHANNEL_TRANSPORT
                            . '_failed',
                    ],
                    self::CHANNEL_TRANSPORT . '_failed'
                        => 'atoolo-channel://failed',
                ],
                'routing' => [
                    ResourceChangeMessage::class => self::CHANNEL_TRANSPORT,
                ],
            ],
        ]);
    }
}
