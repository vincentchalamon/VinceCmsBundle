<?php

/*
 * This file is part of the VinceCmsBundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Listener;

use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Component\DependencyInjection\Container;
use Vince\Bundle\CmsBundle\Event\CmsEvent;

/**
 * Description of SearchListener
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class SearchListener
{

    /** @var $finder TransformedFinder */
    protected $finder;

    /** @var $container Container */
    protected $container;

    public function onLoad(CmsEvent $event)
    {
        // Retrieve search query from Request
        $event->addOption('query', trim(str_ireplace('/', '', $this->container->get('request')->get('query'))));

        // No search query
        if (!$event->getOption('query')) {
            $event->addOption('results', array());

            return;
        }

        // Prepare Query
        $repository = $this->container->get('doctrine.orm.entity_manager')->getRepository('VinceCmsBundle:Article');
        $query      = $repository->createSearchQuery($event->getOption('query'));

        // Send results to controller
        $event->addOption('results', $this->finder->find($query));
    }

    public function setFinder(TransformedFinder $finder)
    {
        $this->finder = $finder;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}