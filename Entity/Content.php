<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Entity;

/**
 * This entity provides features to manage contents
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
abstract class Content
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $contents;

    /**
     * @var Article
     */
    protected $article;

    /**
     * @var Area
     */
    protected $area;

    /**
     * Get contents
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return string
     */
    public function __toString()
    {
        return $this->getContents() ?: '-';
    }

    /**
     * Clone object for translation
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function __clone()
    {
        if (!is_null($this->id)) {
            $this->id = null;
            $this->article = null;
        }
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
     * Set contents
     *
     * @param  string  $contents
     * @return Content
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Get contents
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Set article
     *
     * @param  Article $article
     * @return Content
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set area
     *
     * @param  Area    $area
     * @return Content
     */
    public function setArea(Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return Area
     */
    public function getArea()
    {
        return $this->area;
    }
}
