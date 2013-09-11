<?php

/*
 * This file is part of the VinceCmsBundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\ExecutionContext;

/**
 * This entity provides features to manage an Article.
 * An Article is a page with some Areas through a Template.
 * It also have some Metas and additional features (url, publication...).
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class Article
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var array
     */
    private $tags = array();

    /**
     * @var string
     */
    private $url;

    /**
     * @var \DateTime
     */
    private $startedAt;

    /**
     * @var \DateTime
     */
    private $endedAt;

    /**
     * @var Collection
     */
    private $metas;

    /**
     * @var Collection
     */
    private $contents;

    /**
     * @var Collection
     */
    private $menus;

    /**
     * @var Collection
     */
    private $categories;

    /**
     * @var Template
     */
    private $template;

    /**
     * Build Article
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->menus      = new ArrayCollection();
        $this->metas      = new ArrayCollection();
        $this->contents   = new ArrayCollection();
    }

    /**
     * Render title
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Get specific meta by its name
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $name
     *
     * @return ArticleMeta
     */
    public function getMeta($name)
    {
        return $this->getMetas()->filter(function ($meta) use ($name) {
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
     * @return string
     */
    public function getContent($name)
    {
        return $this->getContents()->filter(function ($content) use ($name) {
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
        return $this->getSlug() == 'homepage' ? $this->getSlug() : 'cms_'.$this->getSlug();
    }

    /**
     * Get route pattern from url or slug fields
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @return string
     */
    public function getRoutePattern()
    {
        return $this->getUrl() ? : '/'.$this->getSlug();
    }

    /**
     * Force publication for homepage
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     */
    public function initHomepage()
    {
        if ($this->getSlug() == 'homepage') {
            $this->startedAt = new \DateTime('now');
            $this->endedAt   = null;
            $this->url       = '/';
        }
    }

    /**
     * Check if publication is correct
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param ExecutionContext $context
     */
    public function isPublicationValid(ExecutionContext $context)
    {
        // No start publication date specified for the end publication date
        if (!$this->getStartedAt() && $this->getEndedAt()) {
            $context->addViolationAt('startedAt', 'Start publication date is required when setting end publication date.');
        }
        // Start publication date is equal or later than end publication date
        if ($this->getEndedAt() && $this->getStartedAt() && $this->getStartedAt()->getTimestamp() >= $this->getEndedAt()->getTimestamp()) {
            $context->addViolationAt('endedAt', 'End publication date must be later than start publication date.');
        }
    }

    /**
     * Check if Article is published
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @return bool
     */
    public function isPublished()
    {
        return $this->getStartedAt()
            && $this->getStartedAt()->getTimestamp() <= time()
            && (!$this->getEndedAt() || $this->getEndedAt()->getTimestamp() >= time());
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
     * Set startedAt
     *
     * @param \DateTime $startedAt
     *
     * @return Article
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set endedAt
     *
     * @param \DateTime $endedAt
     *
     * @return Article
     */
    public function setEndedAt($endedAt)
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * Get endedAt
     *
     * @return \DateTime
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }

    /**
     * Add metas
     *
     * @param ArticleMeta $metas
     *
     * @return Article
     */
    public function addMeta(ArticleMeta $metas)
    {
        $this->metas[] = $metas;

        return $this;
    }

    /**
     * Remove metas
     *
     * @param ArticleMeta $metas
     */
    public function removeMeta(ArticleMeta $metas)
    {
        $this->metas->removeElement($metas);
    }

    /**
     * Get metas
     *
     * @return Collection
     */
    public function getMetas()
    {
        return $this->metas;
    }

    /**
     * Add contents
     *
     * @param Content $contents
     *
     * @return Article
     */
    public function addContent(Content $contents)
    {
        $this->contents[] = $contents;

        return $this;
    }

    /**
     * Remove contents
     *
     * @param Content $contents
     */
    public function removeContent(Content $contents)
    {
        $this->contents->removeElement($contents);
    }

    /**
     * Get contents
     *
     * @return Collection
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Add menus
     *
     * @param Menu $menus
     *
     * @return Article
     */
    public function addMenu(Menu $menus)
    {
        $this->menus[] = $menus;

        return $this;
    }

    /**
     * Remove menus
     *
     * @param Menu $menus
     */
    public function removeMenu(Menu $menus)
    {
        $this->menus->removeElement($menus);
    }

    /**
     * Get menus
     *
     * @return Collection
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

    /**
     * Add categories
     *
     * @param Category $categories
     * @return Article
     */
    public function addCategorie(Category $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param Category $categories
     */
    public function removeCategorie(Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }
}