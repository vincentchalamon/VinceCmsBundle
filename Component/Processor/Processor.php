<?php

/*
 * This file is part of the VinceCmsBundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Component\Processor;

use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of Processor.php
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
abstract class Processor
{

    /** @var $factory FormFactory */
    protected $factory;

    protected $options = array();

    abstract public function process(Request $request);

    public function setFormFactory(FormFactory $formFactory)
    {
        $this->factory = $formFactory;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function createForm($type, $data = null, array $options = array())
    {
        return $this->factory->create($type, $data, $options);
    }
}