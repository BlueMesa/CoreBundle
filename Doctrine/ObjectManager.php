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

use JMS\DiExtraBundle\Annotation as DI;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManagerDecorator;

use Bluemesa\Bundle\CoreBundle\Repository\EntityRepository;
use Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface;
use Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface;


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
     * Class managed by this ObjectManager
     */
    const MANAGED_CLASS = 'Bluemesa\Bundle\CoreBundle\Entity\Entity';
    
    
    /**
     * Set managerRegistry
     *
     * @DI\InjectParams({"managerRegistry" = @DI\Inject("doctrine")})
     * 
     * @param Doctrine\Common\Persistence\ManagerRegistry  $managerRegistry
     */
    public function setManagerRegistry(ManagerRegistry $managerRegistry)
    {
        $this->wrapped = $managerRegistry->getManager();
    }
    
    /**
     * Get the class this Manager is used for
     * 
     * @return string
     */
    public function getManagedClass()
    {
        return static::MANAGED_CLASS;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRepository($className)
    {
        $repository = $this->wrapped->getRepository($className);

        if (! $repository instanceof EntityRepository) {
            throw new \ErrorException('Repository must be an instance of Bluemesa\Bundle\CoreBundle\Repository\EntityRepository');
        }

        return $repository;
    }

    /**
     * Finds an object by its identifier.
     *
     * This is just a convenient shortcut for getRepository($className)->find($id).
     *
     * @param string $className The class name of the object to find.
     * @param mixed  $id        The identity of the object to find.
     * @param Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface $filter
     * 
     * @return object The found object.
     */
    public function find($className, $id, $filter = null)
    {
        if (($filter !== null)&&(! $filter instanceof EntityFilterInterface)) {
            throw new \InvalidArgumentException('Argument 3 passed to '
                    . get_class($this) . ' must be an instance of '
                    . 'Bluemesa\Bundle\CoreBundle\Filter\EntityFilterInterface, '
                    . ((($type = gettype($filter)) == 'object') ? get_class($filter) : $type)
                    . ' given');
        }
        
        $repository = $this->getRepository($className);
        
        return $repository->getEntity($id, $filter);
    }

    /**
     * Finds all entities of the specified type.
     *
     * @param string $className The class name of the objects to find.
     * @param Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface $filter
     * 
     * @return Doctrine\Common\Collections\Collection The entities.
     */
    public function findAll($className, ListFilterInterface $filter = null)
    {
        $repository = $this->getRepository($className);

        return $repository->getList($filter);
    }

    /**
     * Counts all entities of the specified type.
     * 
     * @param string $className The class name of the objects to find.
     * @param Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface $filter
     * 
     * @return integer Number of entities.
     */
    public function countAll($className, ListFilterInterface $filter = null)
    {
        $repository = $this->getRepository($className);

        return $repository->getListCount($filter);
    }
}
