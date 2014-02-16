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

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\SecurityContextInterface;
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
            'render_menu'  => new \Twig_Function_Method($this, 'renderMenu', array('is_safe' => array('html'))),
            'render_block' => new \Twig_Function_Method($this, 'renderBlock', array('is_safe' => array('html')))
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
        $block = $this->repositories['menu']->findOneBy(array('name' => $name));
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