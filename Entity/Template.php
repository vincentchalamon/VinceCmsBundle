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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * This entity provides features to manage a Template.
 * A Template is an Article layout with Areas.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class Template
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var Collection
     */
    protected $areas;

    /**
     * Build Template
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function __construct()
    {
        $this->areas = new ArrayCollection();
    }

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
     * @param  string   $title
     * @return Template
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set path
     *
     * @param  string   $path
     * @return Template
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Add areas
     *
     * @param  Area     $areas
     * @return Template
     */
    public function addArea(Area $areas)
    {
        $this->areas[] = $areas;

        return $this;
    }

    /**
     * Remove areas
     *
     * @param Area $areas
     */
    public function removeArea(Area $areas)
    {
        $this->areas->removeElement($areas);
    }

    /**
     * Get areas
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return Collection
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * Get area by name
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $name Area name
     *
     * @return Area|null
     */
    public function getArea($name)
    {
        foreach ($this->areas as $area) {
            /** @var Area $area */
            if ($area->getName() == $name) {
                return $area;
            }
        }

        return null;
    }
}
