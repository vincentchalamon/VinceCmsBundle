<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Entity;

use Vince\Bundle\CmsBundle\Entity\Area;
use Vince\Bundle\CmsBundle\Entity\Article;
use Vince\Bundle\CmsBundle\Entity\ArticleMeta;
use Vince\Bundle\CmsBundle\Entity\Content;
use Vince\Bundle\CmsBundle\Entity\Meta;
use Vince\Bundle\CmsBundle\Entity\Template;

/**
 * Test Article
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ArticleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Article
     *
     * @var Article|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $article;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->article = $this->getMockForAbstractClass('\Vince\Bundle\CmsBundle\Entity\Article');
    }

    /**
     * Test publication
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testPublication()
    {
        $this->assertEquals('Never published', $this->article->getPublication());
        $this->assertFalse($this->article->isPublished());

        $this->article->setStartedAt(new \DateTime());
        $this->assertEquals('Published', $this->article->getPublication());
        $this->assertTrue($this->article->isPublished());

        $this->article->setStartedAt(new \DateTime('tomorrow'));
        $this->assertEquals('Pre-published', $this->article->getPublication());
        $this->assertFalse($this->article->isPublished());

        $this->article->setStartedAt(new \DateTime('yesterday'));
        $this->article->setEndedAt(new \DateTime('yesterday'));
        $this->assertEquals('Post-published', $this->article->getPublication());
        $this->assertFalse($this->article->isPublished());

        $this->article->setEndedAt(new \DateTime('tomorrow'));
        $this->assertEquals('Published temp', $this->article->getPublication());
        $this->assertTrue($this->article->isPublished());
    }

    /**
     * Test homepage initialize
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testHomepage()
    {
        $this->article->setSlug('homepage');
        $this->article->initHomepage();
        $this->assertNull($this->article->getEndedAt());
        $this->assertEquals(new \DateTime(), $this->article->getStartedAt());
        $this->assertEquals('/', $this->article->getUrl());
        $this->assertEquals('/', $this->article->getRoutePattern());
        $this->assertEquals('homepage', $this->article->getRouteName());
    }

    /**
     * Test methods
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testMethods()
    {
        // Test toString
        $this->article->setTitle('Example');
        $this->assertEquals('Example', $this->article->__toString());

        // Test routing
        $this->article->setSlug('example');
        $this->assertEquals('cms_example', $this->article->getRouteName());
        $this->assertEquals('/example', $this->article->getRoutePattern());
    }

    /**
     * Test get meta
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testGetMeta()
    {
        $meta = new Meta();
        $meta->setName('test');
        /** @var ArticleMeta|\PHPUnit_Framework_MockObject_MockObject $articleMeta */
        $articleMeta = $this->getMockForAbstractClass('\Vince\Bundle\CmsBundle\Entity\ArticleMeta');
        $articleMeta->setMeta($meta);
        $articleMeta->setContents('Example');
        $this->article->addMeta($articleMeta);
        $this->assertEquals($articleMeta, $this->article->getMeta('test'));
        $this->assertEquals('Example', $this->article->getMeta('test')->getContents());

        $this->article->removeMeta($articleMeta);
        $this->assertFalse($this->article->getMeta('test'));
    }

    /**
     * Test get content
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testGetContent()
    {
        $template = new Template();
        $template->setSlug('test');
        $area = new Area();
        $area->setName('test');
        $area->setTemplate($template);
        $this->article->setTemplate($template);
        /** @var Content $content */
        $content = $this->getMockForAbstractClass('\Vince\Bundle\CmsBundle\Entity\Content');
        $content->setArea($area);
        $content->setContents('Example');
        $this->article->addContent($content);
        $this->assertEquals($content, $this->article->getContent('test'));
        $this->assertEquals('Example', $this->article->getContent('test')->getContents());

        $this->article->removeContent($content);
        $this->assertFalse($this->article->getContent('test'));
    }
}