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

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Vince\Bundle\CmsBundle\Entity\Menu;

/**
 * This class provides features to find Menus.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class MenuRepository extends NestedTreeRepository
{

    /**
     * Find a published Menu by its slug
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string $slug Menu slug
     *
     * @return array
     */
    public function findPublishedTreeForMenu($slug)
    {
        // Find published Menu by its slug
        $builder = $this->createQueryBuilder('m')
                        ->where('m.slug = :slug')->setParameter('slug', $slug)
                        ->andWhere('m.lvl = 0');
        /** @var Menu $menu */
        $menu = $builder->andWhere($builder->expr()->andX(
            $builder->expr()->isNotNull('m.startedAt'),
            $builder->expr()->lte('m.startedAt', ':now'),
            $builder->expr()->orX(
                $builder->expr()->isNull('m.endedAt'),
                $builder->expr()->gte('m.endedAt', ':now')
            )
        ))->setParameter('now', new \DateTime())->getQuery()->setMaxResults(1)->getOneOrNullResult();

        // Cannot find Menu
        if ($menu) {
            return null;
        }

        // Build tree from Menu
        $builder = $this->createQueryBuilder('m')
                        ->where('m.root = :root')->setParameter('root', $menu->getId())
                        ->leftJoin('m.article', 'a')->addSelect('a')
                        ->orderBy('m.lft', 'ASC');
        $builder->andWhere($builder->expr()->andX(
            $builder->expr()->isNotNull('m.startedAt'),
            $builder->expr()->lte('m.startedAt', ':now'),
            $builder->expr()->orX(
                $builder->expr()->isNull('m.endedAt'),
                $builder->expr()->gte('m.endedAt', ':now')
            )
        ))->setParameter('now', new \DateTime());

        return $this->buildTree($builder->getQuery()->getArrayResult());
    }
}
