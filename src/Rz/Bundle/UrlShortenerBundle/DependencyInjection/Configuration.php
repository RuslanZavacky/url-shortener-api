<?php
namespace Rz\Bundle\UrlShortenerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $rootNode = $builder->root('url_shortener');

        $rootNode
            ->children()
                ->arrayNode('messaging')
                    ->children()
                        ->scalarNode('enabled')->defaultFalse()->end()
                        ->scalarNode('producer_name')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('stats')
                    ->children()
                        ->scalarNode('enabled')->defaultFalse()->end()
            ->end()
        ;

        return $builder;
    }
} 