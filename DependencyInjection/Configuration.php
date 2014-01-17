<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vince_cms')
            ->children()
                ->arrayNode('class')
                    ->isRequired()
                    ->children()
                        ->scalarNode('article')
                            ->isRequired()
                        ->end()
                        ->scalarNode('block')
                            ->isRequired()
                        ->end()
                        ->scalarNode('menu')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('contact')
                    ->isRequired()
                    ->children()
                        ->scalarNode('noreply')
                            ->isRequired()
                        ->end()
                        ->scalarNode('recipient')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
