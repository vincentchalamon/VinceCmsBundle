<?php

/*
 * This file is part of the Symfony package.
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

    protected $factory;

    abstract public function process(Request $request);

    public function setFormFactory(FormFactory $formFactory)
    {
        $this->factory = $formFactory;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function createForm($type, $data = null, array $options = array())
    {
        return $this->factory->create($type, $data, $options);
    }
}