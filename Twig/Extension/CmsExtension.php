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

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Vince\Bundle\CmsBundle\Entity\Block;

/**
 * Twig extension for CMS
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class CmsExtension extends \Twig_Extension
{

    /**
     * EntityManager
     *
     * @var $manager EntityManager
     */
    protected $manager;

    /**
     * SecurityContextInterface
     *
     * @var $security SecurityContextInterface
     */
    protected $security;

    /**
     * Container
     *
     * @var $container ContainerInterface
     */
    protected $container;

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
        $menu = $this->manager->getRepository($this->container->getParameter('vince.class.menu'))->findOneBy(array('slug' => $slug, 'lvl' => 0));
        if (!$menu || !$menu->getChildren()->count() || (!$menu->isPublished() && !$this->security->isGranted('ROLE_ADMIN'))) {
            return null;
        }

        return $this->container->get('templating')->render($view, array_merge(array('menu' => $menu), $parameters));
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
        /** @var $block Block */
        $block = $this->manager->getRepository($this->container->getParameter('vince.class.block'))->findOneBy(array('name' => $name));
        if (!$block || (!$block->isPublished() && !$this->security->isGranted('ROLE_ADMIN'))) {
            return null;
        }

        return $block->getContents();
    }

    /**
     * Set entity manager
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param EntityManager $manager Entity manager
     */
    public function setEntityManager(EntityManager $manager)
    {
        $this->manager = $manager;
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
     * Set container
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vince_cms';
    }
}