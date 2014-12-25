<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <http://www.vincent-chalamon.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This entity provides features to manage an Area.
 * An Area is a part of a Template, and can be recognized by its name.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="area")
 */
class Area
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     */
    protected $type;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $options = array();

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $required = false;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Vince\Bundle\CmsBundle\Entity\Template", inversedBy="areas", cascade={"all"})
     * @ORM\JoinColumn(name="template_id")
     *
     * @Assert\NotBlank
     */
    protected $template;

    /**
     * Render title
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ?: '-';
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Area
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Area
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param  string $type
     * @return Area
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set options
     *
     * @param array $options
     *
     * @return Area
     */
    public function setOptions(array $options = array())
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set required
     *
     * @param  boolean $required
     * @return Area
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Set template
     *
     * @param  Template $template
     * @return Area
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
