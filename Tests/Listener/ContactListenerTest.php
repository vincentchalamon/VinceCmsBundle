<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Listener;

use Symfony\Component\Form\Test\TypeTestCase;
use Vince\Bundle\CmsBundle\Entity\Article;
use Vince\Bundle\CmsBundle\Event\CmsEvent;
use Vince\Bundle\CmsBundle\Form\Type\ContactType;
use Vince\Bundle\CmsBundle\Listener\ContactListener;

/**
 * Test ContactListener
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ContactListenerTest extends TypeTestCase
{

    /**
     * Test load form
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testOnLoad()
    {
        /** @var Article $article */
        $article  = $this->getMock('\Vince\Bundle\CmsBundle\Entity\Article');
        $listener = new ContactListener();
        $event    = new CmsEvent($article);
        $listener->setFormFactory($this->factory);
        $listener->onLoad($event);
        $this->assertEquals($this->factory->create(new ContactType())->createView(), $event->getOption('form'));
        $this->assertEquals($article, $event->getOption('article'));
    }
}