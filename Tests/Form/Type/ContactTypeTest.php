<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Form\Type;

use Vince\Bundle\CmsBundle\Form\Data\Contact;
use Vince\Bundle\CmsBundle\Form\Type\ContactType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Test ContactType submission
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ContactTypeTest extends TypeTestCase
{

    /**
     * Test ContactType submission
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testSubmitContact()
    {
        $type    = new ContactType();
        $form    = $this->factory->create($type);
        $contact = new Contact();
        $contact->setName('John DOE');
        $contact->setEmail('test@gmail.com');
        $contact->setMessage('Hello World !');
        $form->submit(array(
                'name' => 'John DOE',
                'email' => 'test@gmail.com',
                'message' => 'Hello World !'
            ));

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($contact, $form->getData());
    }
}