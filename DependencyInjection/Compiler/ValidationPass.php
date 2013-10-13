<?php

/*
 * This file is part of the VinceCmsBundle package.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Finder\Finder;

/**
 * Registers the additional validators according to the storage
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ValidationPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('validator.mapping.loader.xml_files_loader.mapping_files')) {
            return;
        }

        $files = $container->getParameter('validator.mapping.loader.xml_files_loader.mapping_files');
        foreach (Finder::create()->files()->name('*.yml')->in(__DIR__ . '/../../Resources/config/validation/') as $file) {
            $files[] = realpath($file);
            $container->addResource(new FileResource($file));
        }

        $container->setParameter('validator.mapping.loader.xml_files_loader.mapping_files', $files);
    }
}
