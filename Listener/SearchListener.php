<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Listener;

use FOS\ElasticaBundle\Finder\TransformedFinder;
use Vince\Bundle\CmsBundle\Entity\Repository\ArticleRepository;
use Vince\Bundle\CmsBundle\Event\CmsEvent;

/**
 * Listen to load CMS article `search`
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class SearchListener
{

    /**
     * Finder
     *
     * @var TransformedFinder
     */
    protected $finder;

    /**
     * ArticleRepository
     *
     * @var ArticleRepository
     */
    protected $ar;

    /**
     * On load article
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param CmsEvent $event
     */
    public function onLoad(CmsEvent $event)
    {
        // Retrieve search query from Request
        $event->addOption('query', trim(str_ireplace('/', '', $event->getOption('request')->get('query'))));

        // No search query
        if (!$event->getOption('query')) {
            $event->addOption('results', array());

            return;
        }

        // Prepare Query
        $query = $this->ar->createSearchQuery($event->getOption('query'));

        // Send results to controller
        $event->addOption('results', $this->finder->find($query));
    }

    /**
     * Set Finder
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param TransformedFinder $finder
     */
    public function setFinder(TransformedFinder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Set ArticleRepository
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param ArticleRepository $ar
     */
    public function setArticleRepository(ArticleRepository $ar)
    {
        $this->ar = $ar;
    }
}