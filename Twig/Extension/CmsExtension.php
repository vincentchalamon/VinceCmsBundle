<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Twig\Extension;

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Vince\Bundle\CmsBundle\Entity\Article;
use Vince\Bundle\CmsBundle\Entity\ArticleMeta;
use Vince\Bundle\CmsBundle\Entity\Menu;
use Vince\Bundle\CmsBundle\Entity\Block;

/**
 * Twig extension for CMS
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class CmsExtension extends \Twig_Extension
{

    /**
     * Repositories
     *
     * @var array
     */
    protected $repositories;

    /**
     * SecurityContextInterface
     *
     * @var SecurityContextInterface
     */
    protected $security;

    /**
     * Environment
     *
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'render_meta'  => new \Twig_Function_Method($this, 'renderMeta', array('is_safe' => array('html'))),
            'render_menu'  => new \Twig_Function_Method($this, 'renderMenu', array('is_safe' => array('html'))),
            'render_block' => new \Twig_Function_Method($this, 'renderBlock', array('is_safe' => array('html')))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('content', array($this, 'renderContents'), array('is_safe' => array('html')))
        );
    }

    /**
     * Render a Menu
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $slug       Menu slug
     * @param string $view       View path
     * @param array  $parameters View parameters
     *
     * @return null|string
     */
    public function renderMenu($slug, $view = 'VinceCmsBundle:Component:menu.html.twig', array $parameters = array())
    {
        /** @var Menu $menu */
        $menu = $this->repositories['menu']->findOneBy(array('slug' => $slug, 'lvl' => 0));
        if (!$menu || !$menu->getChildren()->count() || (!$menu->isPublished() && !$this->security->isGranted('ROLE_ADMIN'))) {
            return null;
        }

        return $this->environment->render($view, array_merge(array('menu' => $menu), $parameters));
    }

    /**
     * Render a meta
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param ArticleMeta $meta
     *
     * @return string
     */
    public function renderMeta(ArticleMeta $meta)
    {
        /** @var \Twig_Template $template */
        $template  = $this->environment->loadTemplate('VinceCmsBundle::meta.html.twig');
        $blockname = str_ireplace(array(':', '-'), array('_', '_'), Inflector::tableize($meta->getMeta()->getName())).'_meta';
        if ($template->hasBlock($blockname)) {
            return $template->renderBlock($blockname, array(
                    'name'     => $meta->getMeta()->getName(),
                    'contents' => $meta->getContents()
                )
            );
        }

        return $template->renderBlock('meta', array(
                'name'     => $meta->getMeta()->getName(),
                'contents' => $meta->getContents()
            )
        );
    }

    /**
     * Render an Article content
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Article $article Article object
     * @param string  $name    Contents name
     *
     * @return null|string
     */
    public function renderContents(Article $article, $name)
    {
        $content = $article->getContent($name);

        return $content ? $content->getContents() : null;
    }

    /**
     * Render a Block
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $name Block name
     *
     * @return null|string
     */
    public function renderBlock($name)
    {
        /** @var Block $block */
        $block = $this->repositories['block']->findOneBy(array('name' => $name));
        if (!$block || (!$block->isPublished() && !$this->security->isGranted('ROLE_ADMIN'))) {
            return null;
        }

        return $block->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Add repository
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string           $name       Name
     * @param EntityRepository $repository Repository
     */
    public function addRepository($name, EntityRepository $repository)
    {
        $this->repositories[$name] = $repository;
    }

    /**
     * Set security context
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param SecurityContextInterface $security Security context
     */
    public function setSecurityContext(SecurityContextInterface $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vince_cms';
    }
}