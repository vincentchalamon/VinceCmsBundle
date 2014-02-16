<?php

/*
 * This file is part of the VinceCms bundle.
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
 * Listen to load CMS article `contact`
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ContactListener
{

    /**
     * Factory
     *
     * @var FormFactory
     */
    protected $factory;

    /**
     * On load article
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param CmsEvent $event
     */
    public function onLoad(CmsEvent $event)
    {
        $event->addOption('form', $this->factory->create(new ContactType())->createView());
    }

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
}