<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Entity;

use Vince\Bundle\CmsBundle\Entity\Content;

/**
 * Test Content
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ContentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test methods
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testMethods()
    {
        /** @var Content|\PHPUnit_Framework_MockObject_MockObject $content */
        $content = $this->getMockForAbstractClass('\Vince\Bundle\CmsBundle\Entity\Content');
        $content->setContents('Example');
        $this->assertEquals('Example', $content->__toString());
    }
}
