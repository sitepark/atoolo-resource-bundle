<?php

namespace Atoolo\Resource\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AtooloResourceExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $configDir = __DIR__ . '/../../config';
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

        $container->setParameter('atoolo_resource.resources', $config['resources']);
        $this->initResourceCapabilityFactory($config, $container);
    }

    // @phpstan-ignore-next-line
    private function initResourceCapabilityFactory(array $config, ContainerBuilder $container): void
    {
        $capabilityImplementationMap = [];

        foreach ($config['resources'] as $resourceClass => $resourceDefinition) {
            foreach ($resourceDefinition['capabilities'] as $capabilityInterface => $capabilityDefinition) {
                $capabilityImplementationMap[$resourceClass][$capabilityInterface]
                    = $capabilityDefinition['implementation'];
            }
        }
        $resourceCapabilityFactoryDef = $container->getDefinition('atoolo_resource.resource_capability_factory');
        $resourceCapabilityFactoryDef->setArgument('$capabilityImplementationMap', $capabilityImplementationMap);
    }
}
