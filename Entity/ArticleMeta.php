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
 * This entity provides features to manage Contents of an Article Meta.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\MappedSuperclass
 */
abstract class ArticleMeta
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank
     */
    protected $contents;

    /**
     * @var Article
     *
     * @Assert\NotNull
     */
    protected $article;

    /**
     * @var Meta
     *
     * @Assert\NotNull
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
