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
 * This entity provides features to manage Contents of an Article Meta.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
abstract class ArticleMeta
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
     * @var Meta
     */
    protected $meta;

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
     * @author Vincent Chalamon <vincent@ylly.fr>
     */
    public function __clone()
    {
        if (!is_null($this->id)) {
            $this->id = null;
            $this->article = null;
            if ($this->getMeta()->getName() == 'language') {
                $this->contents = null;
            }
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
     * @param  string      $contents
     * @return ArticleMeta
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
     * @param  Article     $article
     * @return ArticleMeta
     */
    public function setArticle(Article $article)
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
     * Set meta
     *
     * @param  Meta        $meta
     * @return ArticleMeta
     */
    public function setMeta(Meta $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get meta
     *
     * @return Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }
}
