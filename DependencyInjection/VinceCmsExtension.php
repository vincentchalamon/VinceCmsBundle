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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VinceCmsExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Global parameters
        $keys = array('languages', 'tracking_code', 'domain', 'sitename', 'no_reply', 'contact');
        foreach ($keys as $name) {
            $container->setParameter(sprintf('vince.cms.%s', $name), $config[$name]);
        }
        $container->setParameter('vince.cms', array(
                'languages' => $config['languages'],
                'tracking_code' => $config['tracking_code'],
                'domain' => $config['domain'],
                'sitename' => $config['sitename'],
                'no_reply' => $config['no_reply'],
                'contact' => $config['contact']
            )
        );
        $bundles = $container->getParameter('kernel.bundles');

        // Configure Twig is activated
        if (isset($bundles['TwigBundle']) && $container->hasExtension('twig')) {
            $container->prependExtensionConfig('twig', array(
                    'exception_controller' => 'vince.cms.controller.exception:indexAction',
                    'globals' => array(
                        'vince' => array(
                            'cms' => $container->getParameter('vince.cms'),
                        )
                    )
                )
            );
        }

        // Model parameters
        $config['model']['area']['class'] = 'Vince\Bundle\CmsBundle\Entity\Area';
        $config['model']['area']['repository'] = 'Doctrine\ORM\EntityRepository';
        $config['model']['meta']['class'] = 'Vince\Bundle\CmsBundle\Entity\Meta';
        $config['model']['meta']['repository'] = 'Doctrine\ORM\EntityRepository';
        $config['model']['template']['class'] = 'Vince\Bundle\CmsBundle\Entity\Template';
        $config['model']['template']['repository'] = 'Doctrine\ORM\EntityRepository';

        foreach (array('area', 'article', 'articleMeta', 'block', 'content', 'menu', 'meta', 'template') as $name) {
            $container->setParameter(sprintf('vince.class.%s', $name), $config['model'][$name]['class']);

            // Build repository as service
            $repository = new Definition($config['model'][$name]['repository'], array($config['model'][$name]['class']));
            $repository->setFactoryService('doctrine.orm.default_entity_manager');
            $repository->setFactoryMethod('getRepository');
            $container->setDefinition(sprintf('vince.repository.%s', $name), $repository);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        // Configure Twig is activated
        if (isset($bundles['TwigBundle']) && $container->hasExtension('twig')) {
            $container->prependExtensionConfig('twig', array(
                    'exception_controller' => 'vince.cms.controller.exception:indexAction'
                )
            );
        }

        // Configure Doctrine if DoctrineBundle is activated
        if (isset($bundles['DoctrineBundle']) && $container->hasExtension('doctrine')) {
            $container->prependExtensionConfig('doctrine', array(
                    'orm' => array(
                        'mappings' => array(
                            'tree' => array(
                                'type'   => 'annotation',
                                'alias'  => 'Gedmo',
                                'prefix' => 'Gedmo\Tree\Entity',
                                'dir'    => $container->getParameter('kernel.root_dir').'/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Tree/Entity'
                            )
                        )
                    )
                )
            );
        }
    }
}
