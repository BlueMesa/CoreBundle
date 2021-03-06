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
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;


/**
 * EntityRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntityRepository extends BaseEntityRepository implements EntityRepositoryInterface
{
    use ObjectManagerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct($em, ClassMetadata $class)
    {
        $this->filter = null;
        parent::__construct($em, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function createIndexQueryBuilder()
    {
        return $this->createQueryBuilder('e');
    }

    /**
     * {@inheritdoc}
     */
    public function createIndexQuery()
    {
        return $this->createIndexQueryBuilder()->getQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexResult()
    {
        return $this->createIndexQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createCountQueryBuilder()
    {
        return $this->createIndexQueryBuilder()
            ->select('count(e.id)');
    }

    /**
     * {@inheritdoc}
     */
    public function createCountQuery()
    {
        return $this->createCountQueryBuilder()->getQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexCount()
    {
        return $this->createCountQuery()->getSingleScalarResult();
    }


    /**
     * @deprecated
     * @param  ListFilterInterface  $filter
     * @return Collection
     */
    public function getList(ListFilterInterface $filter = null)
    {
        return $this->getListQuery($filter)->getResult();
    }

    /**
     * @deprecated
     * @param  ListFilterInterface  $filter
     * @return Query
     */
    public function getListQuery(ListFilterInterface $filter = null)
    {
        $qb = $this->getListQueryBuilder($filter);

        return $qb->getQuery();
    }

    /**
     * @deprecated
     * @param  ListFilterInterface  $filter
     * @return QueryBuilder
     */
    protected function getListQueryBuilder(ListFilterInterface $filter = null)
    {
        return $this->createQueryBuilder('e');
    }

    /**
     * @deprecated
     * @param  ListFilterInterface $filter
     * @return integer
     */
    public function getListCount(ListFilterInterface $filter = null)
    {
        return $this->getCountQuery($filter)->getSingleScalarResult();
    }

    /**
     * @deprecated
     * @param  ListFilterInterface $filter
     * @return Query
     */
    public function getCountQuery(ListFilterInterface $filter = null)
    {
        $qb = $this->getCountQueryBuilder($filter);
        
        return $qb->getQuery();
    }

    /**
     * @deprecated
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
     * @deprecated
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
     * @deprecated
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
