<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Entity;

use Vince\Bundle\CmsBundle\Entity\Article;
use Vince\Bundle\CmsBundle\Entity\Menu;

/**
 * Test Menu
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class MenuTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Menu
     *
     * @var Menu|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $menu;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->menu = $this->getMockForAbstractClass('\Vince\Bundle\CmsBundle\Entity\Menu');
    }

    /**
     * Test methods
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testMethods()
    {
        // Test toString
        $this->menu->setTitle('Example');
        $this->assertEquals('Example', $this->menu->__toString());

        // Test routing
        $this->assertFalse($this->menu->isUrl());

        $this->menu->setUrl('/test');
        $this->assertTrue($this->menu->isUrl());
        $this->assertEquals('/test', $this->menu->getRoute());

        /** @var Article $article */
        $article = $this->getMockForAbstractClass('\Vince\Bundle\CmsBundle\Entity\Article');
        $article->setUrl('/example');
        $this->menu->setArticle($article);
        $this->assertFalse($this->menu->isUrl());
        $this->assertEquals('/example', $this->menu->getRoute());
    }
}
