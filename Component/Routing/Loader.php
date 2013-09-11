<?php

/*
 * This file is part of the VinceCmsBundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Component\Routing;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Load routing
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class Loader implements LoaderInterface
{

    private $loaded = false, $em;

    /**
     * Loads a resource
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param mixed  $resource
     * @param string $type
     *
     * @return RouteCollection
     * @throws \RuntimeException
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }
        $routing  = new RouteCollection();
        $articles = $this->em->getRepository('VinceCmsBundle:Article')->findAllIterate();
        while (false !== ($row = $articles->next())) {
            $routing->add($row[0]->getRouteName(), new Route($row[0]->getRoutePattern(), array(
                        '_controller' => 'VinceCmsBundle:Default:show',
                        '_id' => $row[0]->getId()
                    )
                )
            );
            $this->em->detach($row[0]);
        }

        return $routing;
    }

    /**
     * Set entity manager
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Returns true if this class supports the given resource
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param mixed  $resource
     * @param string $type
     *
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return $type === 'cms';
    }

    /**
     * Gets the loader resolver
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return LoaderResolverInterface|void
     */
    public function getResolver()
    {
    }

    /**
     * Sets the loader resolver
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param LoaderResolverInterface $resolver
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
