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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Vince\Bundle\CmsBundle\Validator\Constraints\One;

/**
 * This entity provides features to manage menu
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\MappedSuperclass(repositoryClass="Vince\Bundle\CmsBundle\Entity\Repository\MenuRepository")
 * @Gedmo\Tree(type="nested")
 */
abstract class Menu extends Publishable
{
    /**
     * @var integer
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
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @One(constraints={@Assert\Regex("/^\/[A-z\d\-_\/]*$/"), @Assert\Url})
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Choice(choices={"_blank", "_parent", "_self", "_top"}, message="This value is not a valid target.")
     */
    protected $target = '_self';

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $image = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @var UploadedFile
     *
     * @Assert\Image
     */
    private $file;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Gedmo\TreeLeft
     */
    protected $lft;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Gedmo\TreeRight
     */
    protected $rgt;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\TreeRoot
     */
    protected $root;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Gedmo\TreeLevel
     */
    protected $lvl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @var Collection
     */
    protected $children;

    /**
     * @var Menu
     *
     * @Assert\NotNull
     */
    protected $parent;

    /**
     * @var Article
     */
    protected $article;

    /**
     * Build Menu
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * Get title
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ?: '-';
    }

    /**
     * Get title in admin list
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return string
     */
    public function getAdminListTitle()
    {
        $prefix = '';
        $lvl    = $this->getLvl();
        for ($i = 0; $i < $lvl; $i++) {
            $prefix .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        return $prefix.$this->getTitle();
    }

    /**
     * Check if Menu has url or Article linked if not root
     *
     * @Assert\Callback
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @param ExecutionContextInterface $context
     */
    public function isTargetValid(ExecutionContextInterface $context)
    {
        // Menu is not root and has no url or Article linked
        if ($this->getParent() != $this && !$this->getUrl() && !$this->getArticle()) {
            $context->addViolationAt('url', 'This value should not be blank.');
        }
    }

    /**
     * If Menu is image: check file
     *
     * @Assert\Callback
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @param ExecutionContextInterface $context
     */
    public function isImageValid(ExecutionContextInterface $context)
    {
        if ($this->isImage() && is_null($this->getFile())) {
            $context->addViolationAt('file', 'This value should not be blank.');
        }
    }

    /**
     * Check if Menu has url or Article
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @return bool
     */
    public function isUrl()
    {
        return $this->getUrl() ? true : false;
    }

    /**
     * Get Menu route (url or Article url)
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return string
     */
    public function getRoute()
    {
        return $this->getArticle() ? $this->getArticle()->getRoutePattern() : $this->getUrl();
    }

    /**
     * Set file
     *
     * @param UploadedFile $file
     *
     * @return Menu
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $destinationPath Destination path
     * @param string $publicPath      Public path
     */
    public function upload($destinationPath, $publicPath)
    {
        // Move takes the target directory and target filename as params
        $this->getFile()->move($destinationPath, $this->getFile()->getClientOriginalName());

        // Set the path property to the filename where you've saved the file
        $this->path = rtrim(str_ireplace($publicPath, '', $destinationPath), '/').'/'.$this->getFile()->getClientOriginalName();

        // Clean up the file property as you won't need it anymore
        $this->setFile(null);
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
     * @return Menu
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
     * @param  string $slug
     * @return Menu
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
     * Set url
     *
     * @param  string $url
     * @return Menu
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set image
     *
     * @param  boolean $image
     * @return Menu
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Is image
     *
     * @return boolean
     */
    public function isImage()
    {
        return $this->image;
    }

    /**
     * Set lft
     *
     * @param  integer $lft
     * @return Menu
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param  integer $rgt
     * @return Menu
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param  integer $root
     * @return Menu
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set lvl
     *
     * @param  integer $lvl
     * @return Menu
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Check if menu is root level
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return bool
     */
    public function isRoot()
    {
        return !$this->getLvl();
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Menu
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param  \DateTime $updatedAt
     * @return Menu
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add children
     *
     * @param  Menu $children
     * @return Menu
     */
    public function addChildren(Menu $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param Menu $children
     */
    public function removeChildren(Menu $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param  Menu $parent
     * @return Menu
     */
    public function setParent(Menu $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Menu
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set article
     *
     * @param  Article $article
     * @return Menu
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;
        $this->url = null;

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
     * Set target
     *
     * @param  string $target
     * @return Menu
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set path
     *
     * @param  string $path
     * @return Menu
     */
    public function setPath($path = null)
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
}
