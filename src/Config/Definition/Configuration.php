<?php

namespace PhpZone\PhpZone\Config\Definition;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('phpzone');

        $rootNode
            ->append($this->addExtensionsNode())
            ->append($this->addImportsNode())
        ;

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function addExtensionsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('extensions');

        $node
            ->prototype('variable')
                ->beforeNormalization()
                    ->ifString()
                        ->then(function ($value) {
                            return array($value);
                        })
                ->end()
                ->beforeNormalization()
                    ->ifNull()
                        ->thenEmptyArray()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function addImportsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('imports');

        $node
            ->prototype('array')
                ->children()
                    ->scalarNode('resource')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
