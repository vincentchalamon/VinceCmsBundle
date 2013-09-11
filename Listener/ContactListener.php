<?php

/*
 * This file is part of the VinceCmsBundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Listener;

use Symfony\Component\Form\FormFactory;
use Vince\Bundle\CmsBundle\Event\CmsEvent;
use Vince\Bundle\CmsBundle\Form\Type\ContactType;

/**
 * Description of SearchListener
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ContactListener
{

    /** @var $factory FormFactory */
    protected $factory;

    public function onLoad(CmsEvent $event)
    {
        $event->addOption('form', $this->factory->create(new ContactType())->createView());
    }

    public function setFormFactory(FormFactory $formFactory)
    {
        $this->factory = $formFactory;
    }
}