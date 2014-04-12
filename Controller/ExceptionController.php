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

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as Controller;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Vince\Bundle\CmsBundle\Entity\Article;
use Vince\Bundle\CmsBundle\Entity\Repository\ArticleRepository;
use Vince\Bundle\CmsBundle\Event\CmsEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Exception controller for CMS
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ExceptionController extends Controller
{

    /**
     * Article repository
     *
     * @var ArticleRepository
     */
    protected $repository;

    /**
     * Event dispatcher
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Override constructor
     *
     * @param \Twig_Environment        $twig
     * @param bool                     $debug
     * @param ArticleRepository        $repository
     * @param EventDispatcherInterface $dispatcher
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function __construct(\Twig_Environment $twig, $debug, ArticleRepository $repository, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($twig, $debug);
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Converts an Exception to a Response.
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Request              $request   The request
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     * @param string               $_format   The format to use for rendering (html, xml, ...)
     *
     * @return Response
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     */
    public function indexAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null, $_format = 'html')
    {
        if ($this->debug) {
            return $this->showAction($request, $exception, $logger, $_format);
        }
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $code           = $exception->getStatusCode();
        $template       = $this->findTemplate($request, $_format, $code, $this->debug);
        $options        = array(
            'status_code'    => $code,
            'status_text'    => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
            'exception'      => $exception,
            'logger'         => $logger,
            'currentContent' => $currentContent,
            'request'        => $request
        );
        /** @var Article $article */
        if (!$article = $this->repository->findOneBy(array('slug' => sprintf('error-%s', $code)))) {
            $article = $this->repository->findOneBy(array('slug' => 'error'));
        }
        if ($article) {
            $options  = $this->dispatcher->dispatch('vince.cms.load', new CmsEvent($article, $options))->getOptions();
            $options  = $this->dispatcher->dispatch(sprintf('vince.cms.%s.load', $article->getSlug()), new CmsEvent($article, $options))->getOptions();
            $template = $article->getTemplate()->getPath();
        }

        return new Response($this->twig->render($template, $options));
    }
}
