<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Form\Handler;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vince\Bundle\CmsBundle\Component\Chain\Chain;
use Vince\Bundle\CmsBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
     * Process form
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Request $request Request
     * @param array   $options Options
     *
     * @return bool|Response
     * @throws \InvalidArgumentException
     * @throws NotFoundHttpException
     */
    public function process(Request $request, array &$options = array())
    {
        if (!isset($options['article']) || !$options['article'] || !$options['article'] instanceof Article) {
            throw new NotFoundHttpException('Cannot retrieve article.');
        }

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

                // Processor returns Form object
                } elseif (is_object($return) && $return instanceof Form) {
                    $options['form'] = $return;

                    // Response is build in controller
                    return false;

                // Processor returns array
                } elseif (is_array($return)) {
                    $options = array_merge($return, $options);

                    // Response is build in controller
                    return false;

                // Processor returns boolean
                } elseif (is_bool($return)) {
                    return !$return ?: new RedirectResponse($this->router->generate($article->getRouteName()), 302);
                }
            }
        }

        return false;
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
}
