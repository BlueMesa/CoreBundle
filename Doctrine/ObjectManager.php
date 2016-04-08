<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Doctrine;

use Bluemesa\Bundle\CoreBundle\Entity\EntityInterface;
use Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface;
use Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface;
use Bluemesa\Bundle\CoreBundle\Repository\EntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManagerDecorator;
use JMS\DiExtraBundle\Annotation as DI;


/**
 * Custom implementation of Doctrine\Common\Persistence\ObjectManagerDecorator
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\Service("bluemesa.core.doctrine.manager")
 * @DI\Tag("bluemesa_core.object_manager")
 */
class ObjectManager extends ObjectManagerDecorator
{
    /**
     * @var string  MANAGED_INTERFACE  Interface that classes managed by this ObjectManager must implement
     */
    const MANAGED_INTERFACE = EntityInterface::class;
    
    
    /**
     * Set managerRegistry
     *
     * @DI\InjectParams({"managerRegistry" = @DI\Inject("doctrine")})
     * 
     * @param ManagerRegistry  $managerRegistry
     */
    public function setManagerRegistry(ManagerRegistry $managerRegistry)
    {
        $this->wrapped = $managerRegistry->getManager();
    }
    
    /**
     * Check if this is a good manager for the $class
     * 
     * @param  string         $class
     * @return integer|false
     */
    public function manages($class)
    {
        $managers = array_merge(array(get_class($this)), class_parents($this));
        $score = 0;
                
        foreach ($managers as $manager) {
            if ($manager != ObjectManagerDecorator::class) {
                if (in_array($manager::MANAGED_INTERFACE, class_implements($class))) {
                    $score += 1;
                } else {
                    
                    return false;
                }
            }
        }
        
        return $score;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRepository($className)
    {
        $repository = $this->wrapped->getRepository($className);

        if (! $repository instanceof EntityRepository) {
            throw new \ErrorException('Repository must be an instance of ' . EntityRepository::class);
        }

        return $repository;
    }

    /**
     * Finds an object by its identifier.
     *
     * This is just a convenient shortcut for getRepository($className)->find($id).
     *
     * @param  string                 $className  The class name of the object to find.
     * @param  mixed                  $id         The identity of the object to find.
     * @param  EntityFilterInterface  $filter     Entity filter to apply
     * @return object                             The found object.
     */
    public function find($className, $id, $filter = null)
    {
        if (($filter !== null)&&(! $filter instanceof EntityFilterInterface)) {
            throw new \InvalidArgumentException('Argument 3 passed to '
                    . get_class($this) . ' must be an instance of '
                    . EntityFilterInterface::class . ', '
                    . ((($type = gettype($filter)) == 'object') ? get_class($filter) : $type)
                    . ' given');
        }

        /** @var EntityRepository $repository */
        $repository = $this->getRepository($className);
        
        return $repository->getEntity($id, $filter);
    }

    /**
     * Finds all entities of the specified type.
     *
     * @param  string               $className  The class name of the objects to find.
     * @param  ListFilterInterface  $filter     Entity filter to apply
     * @return Collection                       The entities.
     */
    public function findAll($className, ListFilterInterface $filter = null)
    {
        /** @var EntityRepository $repository */
        $repository = $this->getRepository($className);

        return $repository->getList($filter);
    }

    /**
     * Counts all entities of the specified type.
     * 
     * @param  string               $className  The class name of the objects to find.
     * @param  ListFilterInterface  $filter     Entity filter to apply
     * @return integer                          Number of entities.
     */
    public function countAll($className, ListFilterInterface $filter = null)
    {
        /** @var EntityRepository $repository */
        $repository = $this->getRepository($className);

        return $repository->getListCount($filter);
    }
}
