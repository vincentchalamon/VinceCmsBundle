<?php

/*
 * This file is part of the VinceCmsBundle.
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

class CmsExtension extends \Twig_Extension
{

    /** @var $manager EntityManager */
    protected $manager;

    /** @var $security SecurityContextInterface */
    protected $security;

    /** @var $container ContainerInterface */
    protected $container;

    /**
     * Declare functions for twig
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @return array Functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('menu', array($this, 'renderMenu'), array('is_safe' => array('html')))
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
        $menu = $this->manager->getRepository('VinceCmsBundle:Menu')->findOneBy(array('slug' => $slug, 'lvl' => 0));
        if (!$menu || !$menu->getChildren()->count() || (!$menu->isPublished() && !$this->security->isGranted('ROLE_ADMIN'))) {
            return null;
        }

        return $this->container->get('templating')->render($view, array_merge(array('menu' => $menu), $parameters));
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
     * Get twig extension name
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @return string Twig extension name
     */
    public function getName()
    {
        return 'vince_cms';
    }
}