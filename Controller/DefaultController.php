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
use Symfony\Component\HttpFoundation\Request;

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
        $articles = $this->get('doctrine.orm.default_entity_manager')->getRepository($this->container->getParameter('vince.class.article'))->findAllPublishedOrdered();

        return $this->render('VinceCmsBundle:Templates:sitemap.xml.twig', array(
                'articles' => $articles
            )
        );
    }

    /**
     * Display feed with all published Articles ordered by start publication date
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $_format Request format
     *
     * @return Response
     */
    public function feedAction($_format)
    {
        $format   = $_format == 'xml' ? 'rss' : $_format;
        $articles = $this->get('doctrine.orm.default_entity_manager')->getRepository($this->container->getParameter('vince.class.article'))->findAllPublishedOrdered();

        return $this->render(sprintf('VinceCmsBundle:Templates:feed.%s.twig', $format), array(
                'articles' => $articles,
                'id'       => sha1($this->get('router')->generate('cms_feed', array('_format' => $format), true))
            )
        );
    }

    /**
     * Show an article
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Request $request
     *
     * @return JsonResponse|RedirectResponse|Response
     * @throws NotFoundHttpException
     * @throws \InvalidArgumentException
     */
    public function showAction(Request $request)
    {
        /** @var Article $article */
        $article = $this->getDoctrine()->getRepository($this->container->getParameter('vince.class.article'))->find($request->attributes->get('_id'));
        if (!$article || (!$article->isPublished() && !$this->get('security.context')->isGranted('ROLE_ADMIN'))) {
            throw $this->createNotFoundException();
        }
        // Need to inject request as option because of scope limit on listeners
        $options = $this->get('event_dispatcher')->dispatch('vince.cms.load', new CmsEvent($article, array('request' => $request)))->getOptions();
        $options = $this->get('event_dispatcher')->dispatch(sprintf('vince.cms.%s.load', $article->getSlug()), new CmsEvent($article, $options))->getOptions();
        if ($response = $this->get('vince.cms.form.handler')->process($request, $options)) {
            return $response;
        }

        return $this->render($article->getTemplate()->getPath(), $options)->setStatusCode($request->isMethod('post') ? 400 : 200);
    }
}
