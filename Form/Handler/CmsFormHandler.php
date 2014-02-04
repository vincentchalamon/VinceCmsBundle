<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Form\Handler;

use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Vince\Bundle\CmsBundle\Component\Chain\Chain;
use Vince\Bundle\CmsBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Cms form handler
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class CmsFormHandler
{

    /**
     * Chain
     *
     * @var Chain
     */
    protected $chain;

    /**
     * Router
     *
     * @var Router
     */
    protected $router;

    /**
     * Serializer
     *
     * @var Serializer
     */
    protected $serializer;

    /**
     * Translator
     *
     * @var Translator
     */
    protected $translator;

    /**
     * Session
     *
     * @var Session
     */
    protected $session;

    /**
     * Templating
     *
     * @var EngineInterface
     */
    protected $templating;

    /**
     * Process form
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Request $request Request
     * @param array   $options Options
     *
     * @return JsonResponse|RedirectResponse|Response
     * @throws \InvalidArgumentException
     */
    public function process(Request $request, array &$options = array())
    {
        /** @var Article $article */
        $article = $options['article'];
        if ($request->isMethod('post')) {
            $parameters = array_keys($request->request->all());
            if (!$this->chain->has($parameters[0])) {
                throw new \InvalidArgumentException(sprintf('You must implement a vince.processor tagged service for form %s.', $parameters[0]));
            } else {
                $return = $this->chain->get($parameters[0])->setOptions($options)->process($request);

                // Processor returns a Response object
                if (is_object($return) && $return instanceof Response) {
                    return $return;

                // Processor returns Form object containing errors
                } elseif (is_object($return) && $return instanceof Form) {
                    $options['form'] = $return->createView();

                // Processor returns true, but Request is ajax
                } elseif ($request->isXmlHttpRequest()) {
                    return new JsonResponse();

                // Processor returns true, Request is not ajax
                } else {
                    return new RedirectResponse($this->router->generate($article->getRouteName()), 302);
                }
            }

            if ($request->isXmlHttpRequest()) {
                return new Response($this->serializer->serialize(array_merge(array(
                            'message' => $this->translator->trans('Form has errors.', array(), 'validators')
                        ), $return), 'json'), 400);
            }
            $this->session->getFlashBag()->add('error', $this->translator->trans('Form has errors.', array(), 'validators'));

            //return $this->templating->renderResponse($article->getTemplate()->getPath(), $options)->setStatusCode(400);
        }
    }

    /**
     * Set Chain
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Chain $chain
     *
     * @return CmsFormHandler
     */
    public function setChain($chain)
    {
        $this->chain = $chain;

        return $this;
    }

    /**
     * Set Router
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Router $router
     *
     * @return CmsFormHandler
     */
    public function setRouter($router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Set Serializer
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Serializer $serializer
     *
     * @return CmsFormHandler
     */
    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Set Session
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Session $session
     *
     * @return CmsFormHandler
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Set Templating
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param EngineInterface $templating
     *
     * @return CmsFormHandler
     */
    public function setTemplating($templating)
    {
        $this->templating = $templating;

        return $this;
    }

    /**
     * Set Translator
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Translator $translator
     *
     * @return CmsFormHandler
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;

        return $this;
    }
}