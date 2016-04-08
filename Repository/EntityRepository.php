<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Repository;

use Bluemesa\Bundle\CoreBundle\DependencyInjection\ObjectManagerAwareTrait;
use Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface;
use Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;


/**
 * EntityRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntityRepository extends BaseEntityRepository
{
    use ObjectManagerAwareTrait;
    
    /**
     *
     * @param  ListFilterInterface  $filter
     * @return Collection
     */
    public function getList(ListFilterInterface $filter = null)
    {
        return $this->getListQuery($filter)->getResult();
    }

    /**
     *
     * @param  ListFilterInterface  $filter
     * @return Query
     */
    public function getListQuery(ListFilterInterface $filter = null)
    {
        $qb = $this->getListQueryBuilder($filter);

        return $qb->getQuery();
    }

    /**
     *
     * @param  ListFilterInterface  $filter
     * @return QueryBuilder
     */
    protected function getListQueryBuilder(ListFilterInterface $filter = null)
    {
        return $this->createQueryBuilder('e');
    }

    /**
     *
     * @param  ListFilterInterface $filter
     * @return integer
     */
    public function getListCount(ListFilterInterface $filter = null)
    {
        return $this->getCountQuery($filter)->getSingleScalarResult();
    }

    /**
     *
     * @param  ListFilterInterface $filter
     * @return Query
     */
    public function getCountQuery(ListFilterInterface $filter = null)
    {
        $qb = $this->getCountQueryBuilder($filter);
        
        return $qb->getQuery();
    }

    /**
     *
     * @param  ListFilterInterface $filter
     * @return QueryBuilder
     */
    protected function getCountQueryBuilder(ListFilterInterface $filter = null)
    {
        return $this->createQueryBuilder('e')
                ->select('count(e.id)');
    }

    /**
     * Get a single Entity by its id
     *
     * @param  mixed                  $id
     * @param  EntityFilterInterface  $filter
     * @return QueryBuilder
     */
    public function getEntity($id, EntityFilterInterface $filter = null)
    {
        return $this->getEntityQueryBuilder($id, $filter)->getQuery()->getSingleResult();
    }

    /**
     * Get Entity Query Builder
     *
     * @param  mixed                 $id
     * @param  EntityFilterInterface $filter
     * @return QueryBuilder
     */
    protected function getEntityQueryBuilder($id, EntityFilterInterface $filter = null)
    {
        return $this->createQueryBuilder('e')
                ->where('e.id = :id')
                ->setParameter('id', $id);
    }
}
