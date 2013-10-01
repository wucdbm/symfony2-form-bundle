<?php

namespace Sirian\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $root = $treeBuilder->root('sirian_form');
        $root
            ->children()
                ->arrayNode('suggest')
                    ->useAttributeAsKey('alias')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('entity')->isRequired()->end()
                        ->arrayNode('search')
                            ->beforeNormalization()
                                ->ifTrue(function($v) { return !is_array($v) && !is_null($v); })
                                ->then(function($v) { return is_bool($v) ? array() : preg_split('/\s*,\s*/', $v); })
                            ->end()
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->scalarNode('idPath')->defaultValue('id')->end()
                        ->scalarNode('textPath')->defaultValue('name')->end()
                        ->scalarNode('manager')->defaultNull()->end()
                        ->scalarNode('alias')->end()
        ;


        return $treeBuilder;
    }
}
