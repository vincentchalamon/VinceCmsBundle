<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Translatable element
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
abstract class Translatable
{

    /**
     * @var string
     */
    protected $language;

    /**
     * @var ArrayCollection
     */
    protected $translations;

    /**
     * @var Translatable
     */
    protected $original;

    /**
     * Build Translatable object
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Init original
     *
     * @author Vincent Chalamon <vincent@ylly.fr>
     */
    public function initOriginal()
    {
        if (!$this->original) {
            $this->original = $this;
        }
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Translatable
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set original
     *
     * @param Translatable $original
     *
     * @return Translatable
     */
    public function setOriginal(Translatable $original)
    {
        $this->original = $original;

        return $this;
    }

    /**
     * Get original
     *
     * @return Translatable
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Add translation
     *
     * @param Translatable $translation
     *
     * @return Translatable
     */
    public function addTranslation(Translatable $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param Translatable $translation
     */
    public function removeTranslation(Translatable $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations
     *
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Check if current translatable element has translation
     *
     * @author Vincent Chalamon <vincent@ylly.fr>
     * @param string $language
     * @return bool
     */
    public function hasTranslation($language)
    {
        return $this->getTranslations()->exists(function ($key, Translatable $translation) use ($language) {
            return $translation->getLanguage() == $language;
        });
    }

    /**
     * Get current translatable element translation
     *
     * @author Vincent Chalamon <vincent@ylly.fr>
     * @param string $language
     * @return Translatable|false
     */
    public function getTranslation($language)
    {
        return $this->getTranslations()->filter(function (Translatable $translation) use ($language) {
            return $translation->getLanguage() == $language;
        })->first();
    }
}
