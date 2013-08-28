<?php

/*
 * This file is part of the Symfony package.
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
 * Description of CmsEvent
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class CmsEvent extends Event
{

    protected $article, $options;

    public function __construct(Article $article)
    {
        $this->article = $article;
        $this->options = array('article' => $article);
    }

    public function getArticle()
    {
        return $this->article;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function addOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    public function removeOption($name)
    {
        unset($this->options[$name]);

        return $this;
    }
}