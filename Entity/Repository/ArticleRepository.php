<?php

/*
 * This file is part of the Symfony package.
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

/**
 * This class provides features to find Articles.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ArticleRepository extends EntityRepository
{

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

    public function findOneByIdJoinTemplate($id)
    {
        return $this->createQueryBuilder('a')
                    ->where('a.id = :id')->setParameter('id', $id)
                    ->innerJoin('a.template', 't')->addSelect('t')
                    ->leftJoin('t.areas', 'r')->addSelect('r')
                    ->leftJoin('a.metas', 'm')->addSelect('m')
                    ->leftJoin('m.meta', 'e')->addSelect('e')
                    ->leftJoin('a.contents', 'c')->addSelect('c')
                    ->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    /**
     * Get all published Articles with specified Category
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $category Category name
     *
     * @return array
     */
    public function findPublishedByCategory($category)
    {
        $builder = $this->createQueryBuilder('a');

        return $builder->innerJoin('a.categories', 'category', Join::WITH, $builder->expr()->eq('category.name', ':name'))
            ->addSelect('category')
            ->andWhere($builder->expr()->andX(
                    $builder->expr()->isNotNull('a.startedAt'),
                    $builder->expr()->lte('a.startedAt', ':now'),
                    $builder->expr()->orX(
                        $builder->expr()->isNull('a.endedAt'),
                        $builder->expr()->gte('a.endedAt', ':now')
                    )
                )
            )->setParameters(array('now' => new \DateTime(), 'name' => $category))->getQuery()->getResult();
    }
}