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
use Elastica\Filter\BoolAnd;
use Elastica\Filter\BoolOr;
use Elastica\Filter\Missing;
use Elastica\Filter\Range;
use Elastica\Query;
use Elastica\Query\QueryString;
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
        // Retrieve query from Request
        $event->addOption('query', trim(str_ireplace('/', '', $this->container->get('request')->get('query'))));

        // No query
        if (!$event->getOption('query')) {
            $event->addOption('results', array());
            return;
        }

        // Build Or filter with Range & Missing (endedAt)
        $filterOr  = new BoolOr();
        $filterEnd = new Range('endedAt', array(
            'from' => date('Y-m-d')
        ));
        $filterMissing = new Missing('endedAt');
        $filterOr->addFilter($filterEnd)->addFilter($filterMissing);

        // Build And filter with Range (startedAt) & Or (endedAt)
        $filterAnd   = new BoolAnd();
        $filterStart = new Range('startedAt', array(
            'to' => date('Y-m-d')
        ));
        $filterAnd->addFilter($filterStart)->addFilter($filterOr);

        // Build QueryString for final Query
        $queryString = new QueryString($event->getOption('query'));
        $queryString->setFields(array('title', 'slug', 'tags', 'url', 'summary', 'contents.contents'));

        // Build Query with filters
        $query = new Query();
        $query->setFilter($filterAnd)->setQuery($queryString);

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