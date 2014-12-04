<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Entity;

use Vince\Bundle\CmsBundle\Entity\ArticleMeta;

/**
 * Test ArticleMeta
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ArticleMetaTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test methods
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testMethods()
    {
        /** @var ArticleMeta|\PHPUnit_Framework_MockObject_MockObject $articleMeta */
        $articleMeta = $this->getMockForAbstractClass('\Vince\Bundle\CmsBundle\Entity\ArticleMeta');
        $articleMeta->setContents('Example');
        $this->assertEquals('Example', $articleMeta->__toString());
    }
}
