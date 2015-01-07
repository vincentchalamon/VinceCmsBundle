<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Entity;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\ConstraintViolationList;
use Vince\Bundle\CmsBundle\Entity\Publishable;
use Vince\Bundle\CmsBundle\Entity\Template;

/**
 * Test Publishable methods.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class PublishableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Publishable object
     *
     * @var Publishable|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $object;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass('\Vince\Bundle\CmsBundle\Entity\Publishable');
    }

    /**
     * Test publication
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testPublication()
    {
        $this->assertEquals('Never published', $this->object->getPublication());
        $this->assertFalse($this->object->isPublished());

        $this->object->setStartedAt(new \DateTime());
        $this->assertEquals('Published', $this->object->getPublication());
        $this->assertTrue($this->object->isPublished());

        $this->object->setStartedAt(new \DateTime('tomorrow'));
        $this->assertEquals('Pre-published', $this->object->getPublication());
        $this->assertFalse($this->object->isPublished());

        $this->object->setStartedAt(new \DateTime('yesterday'));
        $this->object->setEndedAt(new \DateTime('yesterday'));
        $this->assertEquals('Post-published', $this->object->getPublication());
        $this->assertFalse($this->object->isPublished());

        $this->object->setEndedAt(new \DateTime('tomorrow'));
        $this->assertEquals('Published temp', $this->object->getPublication());
        $this->assertTrue($this->object->isPublished());

        $this->object->publish();
        $this->assertEquals('Published', $this->object->getPublication());
        $this->assertTrue($this->object->isPublished());

        $this->object->unpublish();
        $this->assertEquals('Never published', $this->object->getPublication());
        $this->assertFalse($this->object->isPublished());
    }
}
