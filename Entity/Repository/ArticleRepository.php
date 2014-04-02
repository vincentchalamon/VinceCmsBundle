<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Vince\Bundle\CmsBundle\Entity\Article;
use Elastica\Filter\BoolAnd;
use Elastica\Filter\BoolOr;
use Elastica\Filter\Missing;
use Elastica\Filter\Range;
use Elastica\Query;
use Elastica\Query\QueryString;

/**
 * This class provides features to find Articles.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ArticleRepository extends EntityRepository
{

    /**
     * Search published Articles through Elasticsearch
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $search Search
     *
     * @return Query|null
     */
    public function createSearchQuery($search)
    {
        // No search
        if (!$search) {
            return null;
        }

        // Build Or filter with Range & Missing (endedAt)
        $filterOr  = new BoolOr();
        $filterEnd = new Range('endedAt', array(
            'gte' => date('Y-m-d')
        ));
        $filterMissing = new Missing('endedAt');
        $filterOr->addFilter($filterEnd)->addFilter($filterMissing);

        // Build And filter with Range (startedAt) & Or (endedAt)
        $filterAnd   = new BoolAnd();
        $filterStart = new Range('startedAt', array(
            'lte' => date('Y-m-d')
        ));
        $filterAnd->addFilter($filterStart)->addFilter($filterOr);

        // Build QueryString for final Query
        $queryString = new QueryString($search);
        $queryString->setFields(array('title', 'slug', 'tags', 'url', 'summary'));

        // Build Query with filters
        $query = new Query();
        $query->setFilter($filterAnd)->setQuery($queryString);

        return $query;
    }

    /**
     * Find an Article from its identifier
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param integer $id Article identifier
     *
     * @return Article
     */
    public function find($id)
    {
        // Retrieve identifier & id
        if (is_array($id)) {
            $keys       = array_keys($id);
            $identifier = $keys[0];
            $id         = $id[$identifier];
        } else {
            $identifier = $this->_em->getMetadataFactory()->getMetadataFor(ltrim($this->_entityName, '\\'))->identifier[0];
        }

        return $this->createQueryBuilder('a')
                    ->leftJoin('a.metas', 'm')->addSelect('m')
                    ->leftJoin('a.menus', 'me')->addSelect('me')
                    ->leftJoin('a.contents', 'co')->addSelect('co')
                    ->leftJoin('co.area', 'ar')->addSelect('ar')
                    ->leftJoin('a.template', 't')->addSelect('t')
                    ->where(sprintf('a.%s = :id', $identifier))->setParameter('id', $id)
                    ->getQuery()->getOneOrNullResult();
    }

    /**
     * Create builder for indexable articles (non system)
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createIndexableQueryBuilder()
    {
        $builder = $this->createQueryBuilder('a');

        return $builder->where($builder->expr()->notIn('a.slug', array('homepage', 'search')));
    }

    /**
     * Return iterable Articles list
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return IterableResult
     */
    public function findAllIterate()
    {
        return $this->createQueryBuilder('a')->getQuery()->iterate();
    }

    /**
     * Detach entity from iterator
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param object $entity The entity to detach
     */
    public function detach($entity)
    {
        $this->_em->detach($entity);
    }

    /**
     * Get all published Articles ordered by start publication date DESC
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @return array
     */
    public function findAllPublishedOrdered()
    {
        $builder = $this->createQueryBuilder('a')->orderBy('a.startedAt', 'DESC');

        return $builder->where(
            $builder->expr()->andX(
                $builder->expr()->isNotNull('a.startedAt'),
                $builder->expr()->lte('a.startedAt', ':now'),
                $builder->expr()->orX(
                    $builder->expr()->isNull('a.endedAt'),
                    $builder->expr()->gte('a.endedAt', ':now')
                )
            )
        )->setParameters(array('now' => new \DateTime()))->getQuery()->getResult();
    }
}