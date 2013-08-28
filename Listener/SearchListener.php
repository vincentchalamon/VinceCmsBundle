<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Listener;

use FOS\ElasticaBundle\Finder\TransformedFinder;
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

    protected $request;
    protected $container;

    public function onLoad(CmsEvent $event)
    {
        //$event->addOption('results', $this->finder->search($query));
        $event->addOption('results', array());
        $event->addOption('query', $this->container->get('request')->get('query'));
    }

    public function setFinder(TransformedFinder $finder)
    {
        $this->finder = $finder;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }
}