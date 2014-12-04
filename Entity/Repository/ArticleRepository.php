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
use Doctrine\ORM\Query\Expr\Join;
use Vince\Bundle\CmsBundle\Entity\Article;

/**
 * This class provides features to find Articles.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ArticleRepository extends EntityRepository
{

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
                    ->leftJoin('m.meta', 'meta')->addSelect('meta')
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

        /** @todo-vince List slugs in Article entity as constant */

        return $builder->where($builder->expr()->notIn('a.slug', array('homepage', 'search', 'error', 'error-404', 'error-500')));
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

    /**
     * Get all published Articles ordered by start publication date DESC
     * And with index,follow meta
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @return array
     */
    public function findAllPublishedIndexableOrdered()
    {
        $builder = $this->createQueryBuilder('a')->orderBy('a.startedAt', 'DESC');
        $builder->where(
            $builder->expr()->andX(
                $builder->expr()->isNotNull('a.startedAt'),
                $builder->expr()->lte('a.startedAt', ':now'),
                $builder->expr()->orX(
                    $builder->expr()->isNull('a.endedAt'),
                    $builder->expr()->gte('a.endedAt', ':now')
                )
            )
        )->innerJoin('a.metas', 'm', Join::WITH, 'm.contents = :value');

        return $builder->setParameters(array(
                'now'   => new \DateTime(),
                'value' => 'index,follow'
            )
        )->getQuery()->getResult();
    }
}
