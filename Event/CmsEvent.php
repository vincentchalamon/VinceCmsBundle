<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Vince\Bundle\CmsBundle\Entity\Article;

/**
 * CMS event when article is loaded.
 * This event allows Users to add objects to Response
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class CmsEvent extends Event
{

    /**
     * Options
     *
     * @var array
     */
    protected $options;

    /**
     * @param Article $article
     * @param array   $options
     */
    public function __construct(Article $article, array $options = array())
    {
        $this->options = array_merge(array('article' => $article), $options);
    }

    /**
     * Get article
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return Article
     */
    public function getArticle()
    {
        return $this->options['article'];
    }

    /**
     * Get options
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add option
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param mixed $name  Option name
     * @param mixed $value Option value
     *
     * @return CmsEvent
     */
    public function addOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Get option
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param mixed $name Option name
     *
     * @return null|mixed
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * Remove option
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param mixed $name Option name
     *
     * @return CmsEvent
     * @throws \InvalidArgumentException
     */
    public function removeOption($name)
    {
        if ($name == 'article') {
            throw new \InvalidArgumentException('You cannot remote `article` option.');
        }
        unset($this->options[$name]);

        return $this;
    }
}
