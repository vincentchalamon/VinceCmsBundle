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
use My\Bundle\CmsBundle\Entity\ArticleMeta;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * This entity provides features to manage an Article.
 * An Article is a page with some Areas through a Template.
 * It also have some Metas and additional features (url, publication...).
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @ORM\MappedSuperclass(repositoryClass="Vince\Bundle\CmsBundle\Entity\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"url"}, message="This url is already used.")
 */
abstract class Article extends Publishable
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
     *
     * @Gedmo\Slug(fields={"title"})
     */
    protected $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at")
     *
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank
     */
    protected $summary;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $tags = array();

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     *
     * @Assert\Regex("/^\/[A-z\d\-_\/]*$/")
     */
    protected $url;

    /**
     * @var ArrayCollection
     */
    protected $metas;

    /**
     * @var ArrayCollection
     */
    protected $contents;

    /**
     * @var ArrayCollection
     */
    protected $menus;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Vince\Bundle\CmsBundle\Entity\Template", cascade={"persist"})
     * @ORM\JoinColumn(name="template_id", onDelete="CASCADE")
     *
     * @Assert\NotNull
     */
    protected $template;

    /**
     * Build Article
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->metas = new ArrayCollection();
        $this->contents = new ArrayCollection();
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
     * Check if Article is system (homepage, error)
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return bool
     */
    public function isSystem()
    {
        return $this->isHomepage() || substr($this->getSlug(), 0, 5) == 'error';
    }

    /**
     * Check if Article is homepage
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return bool
     */
    public function isHomepage()
    {
        return $this->getSlug() == 'homepage';
    }

    /**
     * Get specific meta by its name
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $name
     *
     * @return bool|ArticleMeta
     */
    public function getMeta($name)
    {
        return $this->getMetas()->filter(function (ArticleMeta $meta) use ($name) {
            return $meta->getMeta()->getName() == $name;
        })->first();
    }

    /**
     * Get Article content for Area
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $name Area name
     *
     * @return bool|Content
     */
    public function getContent($name)
    {
        return $this->getContents()->filter(function (Content $content) use ($name) {
            return $content->getArea()->getName() == $name;
        })->first();
    }

    /**
     * Get route name from slug
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @return string
     */
    public function getRouteName()
    {
        return $this->isSystem() ? $this->getSlug() : 'cms_'.$this->getSlug();
    }

    /**
     * Get route pattern from url or slug fields
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @return string
     */
    public function getRoutePattern()
    {
        return $this->getUrl() ?: '/'.$this->getSlug();
    }

    /**
     * Force publication for system
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @ORM\PrePersist
     */
    public function initHomepage()
    {
        if ($this->isSystem()) {
            $this->publish();
        }
        if ($this->isHomepage()) {
            $this->setUrl('/');
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
     * Set title
     *
     * @param string $title
     *
     * @return Article
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Article
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
     * @param \DateTime $updatedAt
     *
     * @return Article
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
     * Set summary
     *
     * @param string $summary
     *
     * @return Article
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set tags
     *
     * @param array $tags
     *
     * @return Article
     */
    public function setTags(array $tags = array())
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Article
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
     * Add meta
     *
     * @param ArticleMeta $meta
     *
     * @return Article
     */
    public function addMeta(ArticleMeta $meta)
    {
        $meta->setArticle($this);
        $this->metas[] = $meta;

        return $this;
    }

    /**
     * Remove meta
     *
     * @param ArticleMeta $meta
     */
    public function removeMeta(ArticleMeta $meta)
    {
        $this->metas->removeElement($meta);
    }

    /**
     * Get metas
     *
     * @return ArrayCollection
     */
    public function getMetas()
    {
        return $this->metas;
    }

    /**
     * Add contents
     *
     * @param Content $content
     *
     * @return Article
     */
    public function addContent(Content $content)
    {
        if ($content->getArea()->getTemplate()->getSlug() == $this->getTemplate()->getSlug()
            && trim(strip_tags($content->getContents(), '<img><input><button><iframe>'))) {
            $content->setArticle($this);
            $this->contents[] = $content;
        }

        return $this;
    }

    /**
     * Remove content
     *
     * @param Content $content
     */
    public function removeContent(Content $content)
    {
        $this->contents->removeElement($content);
    }

    /**
     * Get contents
     *
     * @return ArrayCollection
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Add menu
     *
     * @param Menu $menu
     *
     * @return Article
     */
    public function addMenu(Menu $menu)
    {
        $menu->setArticle($this);
        $this->menus[] = $menu;

        return $this;
    }

    /**
     * Remove menu
     *
     * @param Menu $menu
     */
    public function removeMenu(Menu $menu)
    {
        $this->menus->removeElement($menu);
    }

    /**
     * Get menus
     *
     * @return ArrayCollection
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Set template
     *
     * @param Template $template
     *
     * @return Article
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
