<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Form\Data;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Vince\Bundle\CmsBundle\Form\Data\Contact;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Test Contact validation.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ContactTest extends WebTestCase
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
     * Test Contact validation
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testContactValidation()
    {
        /** @var Validator $validator */
        $contact   = new Contact();
        $validator = static::$kernel->getContainer()->get('validator');

        // Test invalid object
        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($contact);
        $this->assertCount(3, $errors);

        $contact->setName('DOE');
        $contact->setEmail('test@gmail.com');
        $contact->setMessage('Hello World !');
        $this->assertCount(0, $validator->validate($contact));

        // Test email
        $contact->setEmail('test');
        $errors = $validator->validate($contact);
        $this->assertCount(1, $errors);
        $this->assertEquals('email', $errors->get(0)->getPropertyPath());

        $contact->setEmail('test@qsdfghjn.com');
        $errors = $validator->validate($contact);
        $this->assertCount(1, $errors);
        $this->assertEquals('email', $errors->get(0)->getPropertyPath());

        $contact->setEmail('test@gmail.notagoodextension');
        $errors = $validator->validate($contact);
        $this->assertCount(1, $errors);
        $this->assertEquals('email', $errors->get(0)->getPropertyPath());

        $contact->setEmail('test@gmail.com');
        $this->assertCount(0, $validator->validate($contact));

        // Test message
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $message    = '';
        for ($i = 0; $i < 1001; $i++) {
            $message.= $characters[rand(0, strlen($characters) - 1)];
        }
        $contact->setMessage($message);
        $errors = $validator->validate($contact);
        $this->assertCount(1, $errors);
        $this->assertEquals('message', $errors->get(0)->getPropertyPath());

        $contact->setMessage('Hello World !');
        $this->assertCount(0, $validator->validate($contact));
    }
}