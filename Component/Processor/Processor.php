<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Component\Processor;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base class for forms
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
abstract class Processor
{

    /**
     * Factory
     *
     * @var $factory FormFactory
     */
    protected $factory;

    /**
     * Options
     *
     * @var array
     */
    protected $options = array();

    /**
     * Process form
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Request $request
     *
     * @return bool|Form
     */
    abstract public function process(Request $request);

    /**
     * Set FormFactory
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->factory = $formFactory;
    }

    /**
     * Set options
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Create Form
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $type
     * @param null   $data
     * @param array  $options
     *
     * @return Form|FormInterface
     */
    protected function createForm($type, $data = null, array $options = array())
    {
        return $this->factory->create($type, $data, $options);
    }
}