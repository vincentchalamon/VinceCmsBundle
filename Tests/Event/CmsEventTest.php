<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Event;

use Vince\Bundle\CmsBundle\Entity\Article;
use Vince\Bundle\CmsBundle\Event\CmsEvent;

/**
 * Test CmsEvent
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class CmsEventTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test object methods
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testMethods()
    {
        /** @var Article $article */
        $article = $this->getMock('\Vince\Bundle\CmsBundle\Entity\Article');
        $event   = new CmsEvent($article);
        $this->assertEquals($article, $event->getArticle());
        $this->assertEquals(array('article' => $article), $event->getOptions());
        $this->assertEquals($article, $event->getOption('article'));
        $event->addOption('foo', 'bar');
        $this->assertEquals(array('article' => $article, 'foo' => 'bar'), $event->getOptions());
        $event->removeOption('foo');
        $this->assertEquals(array('article' => $article), $event->getOptions());
    }
}