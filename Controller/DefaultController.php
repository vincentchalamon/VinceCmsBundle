<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vince\Bundle\CmsBundle\Entity\Article;
use Vince\Bundle\CmsBundle\Event\CmsEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for CMS
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class DefaultController extends Controller
{

    /**
     * Generate sitemap
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return Response
     */
    public function sitemapAction()
    {
        $articles = $this->get('doctrine.orm.entity_manager')->getRepository($this->container->getParameter('vince.class.article'))->findAllPublishedOrdered();

        return $this->render('VinceCmsBundle:Templates:sitemap.xml.twig', array(
            'articles' => $articles
        ));
    }

    /**
     * Display feed with all published Articles ordered by start publication date
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return Response
     */
    public function feedAction()
    {
        $format = $this->getRequest()->getRequestFormat() == 'xml' ? 'rss' : $this->getRequest()->getRequestFormat();
        $articles = $this->get('doctrine.orm.entity_manager')->getRepository($this->container->getParameter('vince.class.article'))->findAllPublishedOrdered();

        return $this->render(sprintf('VinceCmsBundle:Templates:feed.%s.twig', $format), array(
            'articles' => $articles,
            'id'       => sha1($this->get('router')->generate('cms_feed', array('_format' => $format), true))
        ));
    }

    /**
     * Show an article
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return JsonResponse|RedirectResponse|Response
     * @throws NotFoundHttpException
     * @throws \InvalidArgumentException
     */
    public function showAction()
    {
        /** @var Article $article */
        $article = $this->getDoctrine()->getRepository($this->container->getParameter('vince.class.article'))->find($this->getRequest()->attributes->get('_id'));
        if (!$article || (!$article->isPublished() && !$this->get('security.context')->isGranted('ROLE_ADMIN'))) {
            throw $this->createNotFoundException();
        }
        $options = $this->get('event_dispatcher')->dispatch('vince.cms.load', new CmsEvent($article))->getOptions();
        $options = $this->get('event_dispatcher')->dispatch(sprintf('vince.cms.%s.load', $article->getSlug()), new CmsEvent($article, $options))->getOptions();
        if ($response = $this->get('vince.cms.form.handler')->process($this->getRequest(), $options)) {
            return $response;
        }

        return $this->render($article->getTemplate()->getPath(), $options);
    }
}
