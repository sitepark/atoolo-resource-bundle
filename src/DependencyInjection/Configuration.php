<?php

namespace Atoolo\Resource\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('atoolo_resource');

        // @phpstan-ignore-next-line
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('resources')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('capabilities')
                                ->useAttributeAsKey('interface')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('implementation')->isRequired()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
