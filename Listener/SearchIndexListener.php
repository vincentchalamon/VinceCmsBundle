<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use EWZ\Bundle\SearchBundle\Lucene\LuceneSearch;
use EWZ\Bundle\SearchBundle\Lucene\Document;
use EWZ\Bundle\SearchBundle\Lucene\Field;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Vince\Bundle\CmsBundle\Entity\Article;

/**
 * Description of LuceneListener
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class SearchIndexListener
{

    /** @var $lucene LuceneSearch */
    protected $lucene;

    /** @var $router Router */
    protected $router;

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Article) {
            $document = new Document();
            $document->addField(Field::keyword('key', $entity->getId()));
            $document->addField(Field::text('title', $entity->getTitle()));
            $document->addField(Field::text('url', $this->router->generate($entity->getRouteName())));
            $document->addField(Field::unstored('body', $entity->getSummary()));
            foreach ($entity->getTags() as $tag) {
                $document->addField(Field::keyword('key', $tag));
            }

            $this->lucene->addDocument($document);
            $this->lucene->updateIndex();
        }
    }

    public function setLucene(LuceneSearch $lucene)
    {
        $this->lucene = $lucene;
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }
}