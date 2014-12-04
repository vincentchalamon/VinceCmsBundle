<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Component\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register tagged services
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class CompilerPass implements CompilerPassInterface
{

    protected $name;

    /**
     * Register tagged services from name
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @param string $name Tag name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Register tagged services
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @param ContainerBuilder $container Container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->name.'.chain')) {
            return;
        }

        $definition = $container->getDefinition($this->name.'.chain');
        $taggedServices = $container->findTaggedServiceIds($this->name);

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall('add', array(new Reference($id), $attributes['alias']));
            }
        }
    }

}
