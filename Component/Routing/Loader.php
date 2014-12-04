<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Component\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Vince\Bundle\CmsBundle\Entity\Article;
use Vince\Bundle\CmsBundle\Entity\Repository\ArticleRepository;

/**
 * Load routing
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class Loader implements LoaderInterface
{

    /**
     * Is loader loaded
     *
     * @var bool
     */
    protected $loaded = false;

    /**
     * Article repository
     *
     * @var ArticleRepository
     */
    protected $repository;

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }
        $routing  = new RouteCollection();
        $articles = $this->repository->findAllIterate();
        while (false !== ($row = $articles->next())) {
            /** @var Article $article */
            $article = $row[0];
            $routing->add($article->getRouteName(), new Route($article->getRoutePattern(), array(
                        '_controller' => 'VinceCmsBundle:Default:show',
                        '_locale' => $article->getLocale(),
                        '_id' => $article->getId()
                    ), array(
                        '_method' => 'GET|POST'
                    )
                )
            );
            $this->repository->detach($article);
        }

        return $routing;
    }

    /**
     * Set Article class
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param ArticleRepository $repository
     */
    public function setArticleRepository(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return $type === 'cms';
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
