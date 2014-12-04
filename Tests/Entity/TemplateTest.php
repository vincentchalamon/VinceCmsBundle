<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\ConstraintViolationList;
use Vince\Bundle\CmsBundle\Entity\Template;

/**
 * Test Template validation.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class TemplateTest extends WebTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    /**
     * Test validation
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testValidation()
    {
        /**
         * @var Validator               $validator
         * @var ConstraintViolationList $errors
         */
        $object    = new Template();
        $validator = static::$kernel->getContainer()->get('validator');

        // Test invalid object
        $errors = $validator->validate($object);
        $this->assertCount(2, $errors);

        $object->setTitle('Example');
        $object->setPath('VinceCmsBundle:Templates:homepage.html.twig');

        $errors = $validator->validate($object);
        $this->assertCount(0, $errors);
    }
}
