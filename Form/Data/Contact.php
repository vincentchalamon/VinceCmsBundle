<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Form\Data;

/**
 * Object for ContactType
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class Contact
{

    /**
     * Name
     *
     * @var string
     */
    protected $name;

    /**
     * Email
     *
     * @var string
     */
    protected $email;

    /**
     * Message
     *
     * @var string
     */
    protected $message;

    /**
     * Set Email
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $email
     *
     * @return Contact
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get Email
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set Message
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $message
     *
     * @return Contact
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get Message
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set Name
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $name
     *
     * @return Contact
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Name
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}