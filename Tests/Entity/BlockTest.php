<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Entity;

use Vince\Bundle\CmsBundle\Entity\Block;

/**
 * Test Block
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class BlockTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Block
     *
     * @var Block|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $block;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->block = $this->getMockForAbstractClass('\Vince\Bundle\CmsBundle\Entity\Block');
    }

    /**
     * Test publication
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testPublication()
    {
        $this->assertEquals('Never published', $this->block->getPublication());
        $this->assertFalse($this->block->isPublished());

        $this->block->setStartedAt(new \DateTime());
        $this->assertEquals('Published', $this->block->getPublication());
        $this->assertTrue($this->block->isPublished());

        $this->block->setStartedAt(new \DateTime('tomorrow'));
        $this->assertEquals('Pre-published', $this->block->getPublication());
        $this->assertFalse($this->block->isPublished());

        $this->block->setStartedAt(new \DateTime('yesterday'));
        $this->block->setEndedAt(new \DateTime('yesterday'));
        $this->assertEquals('Post-published', $this->block->getPublication());
        $this->assertFalse($this->block->isPublished());

        $this->block->setEndedAt(new \DateTime('tomorrow'));
        $this->assertEquals('Published temp', $this->block->getPublication());
        $this->assertTrue($this->block->isPublished());
    }

    /**
     * Test methods
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testMethods()
    {
        // Test toString
        $this->block->setContents('Example');
        $this->assertEquals('Example', $this->block->__toString());
    }
}