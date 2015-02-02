<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <http://www.vincent-chalamon.fr>
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
        // todo-vince Validation
        $treeBuilder->root('vince_cms')
            ->children()
                ->scalarNode('domain')->isRequired()->end()
                ->scalarNode('sitename')->isRequired()->end()
                ->scalarNode('tracking_code')->defaultNull()->end()
                ->scalarNode('no_reply')->defaultValue('no-reply@sandbox.com')->end()
                ->arrayNode('contact')
                    ->prototype('scalar')->end()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($v) { return array($v); })
                    ->end()
                ->end()
                ->arrayNode('model')
                    ->isRequired()
                    ->children()
                        ->arrayNode('article')
                            ->isRequired()
                            ->children()
                                ->scalarNode('class')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('repository')
                                    ->defaultValue('Vince\Bundle\CmsBundle\Entity\Repository\ArticleRepository')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('menu')
                            ->isRequired()
                            ->children()
                                ->scalarNode('class')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('repository')
                                    ->defaultValue('Vince\Bundle\CmsBundle\Entity\Repository\MenuRepository')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('block')
                            ->isRequired()
                            ->children()
                                ->scalarNode('class')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('repository')
                                    ->defaultValue('Doctrine\ORM\EntityRepository')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('articleMeta')
                            ->isRequired()
                            ->children()
                                ->scalarNode('class')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('repository')
                                    ->defaultValue('Doctrine\ORM\EntityRepository')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('content')
                            ->isRequired()
                            ->children()
                                ->scalarNode('class')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('repository')
                                    ->defaultValue('Doctrine\ORM\EntityRepository')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
