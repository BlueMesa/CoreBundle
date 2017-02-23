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
use Doctrine\Common\Persistence\ObjectManager as DoctrineObjectManager;

/**
 * Doctrine Object Manager registry
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\Service("bluemesa.core.doctrine.registry")
 */
class ObjectManagerRegistry
{
    /**
     * @var ManagerRegistry  $doctrineManagerRegistry
     */
    protected $doctrineManagerRegistry;
    
    /**
     * @var array  $managers
     */
    private $managers;

    
    /**
     * Construct ObjectManagerRegistry
     * 
     * @DI\InjectParams({
     *     "managerRegistry" = @DI\Inject("doctrine"),
     * })
     * 
     * @param ManagerRegistry  $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managers = array();
        $this->doctrineManagerRegistry = $managerRegistry;
    }

    /**
     * @return DoctrineObjectManager
     */
    public function getManager()
    {
        return $this->getManagerForClass();
    }

    /**
     * Get manager for class
     *
     * @param  object                 $object
     * @return DoctrineObjectManager
     */
    public function getManagerForClass($object = null)
    {
        if (null == $object) {
            return $this->doctrineManagerRegistry->getManager();
        }
        
        $class = is_object($object) ? get_class($object) : $object;
        $managers = array();
                
        foreach ($this->managers as $manager) {
            $priority = $manager->manages($class);
            $managers[$priority] = $manager;
        }
        
        if (count($managers)) {
            $key = max(array_keys($managers));
            
            return $managers[$key];
        }
        
        return $this->doctrineManagerRegistry->getManagerForClass($class);
    }

    /**
     * Gets the ObjectRepository for an persistent object.
     *
     * @param string $persistentObject      The name of the persistent object.
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($persistentObject)
    {
        return $this->getManagerForClass($persistentObject)->getRepository($persistentObject);
    }

    /**
     * Add ObjectManager to the registry
     * 
     * @param DoctrineObjectManager  $manager
     */
    public function addManager(DoctrineObjectManager $manager)
    {
        $this->managers[] = $manager;
    }
}
