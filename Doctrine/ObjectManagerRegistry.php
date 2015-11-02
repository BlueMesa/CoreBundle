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
     * @var Doctrine\Common\Persistence\ManagerRegistry
     */
    protected $doctrineManagerRegistry;
    
    /**
     * @var array
     */
    private $managers;

    
    /**
     * Construct ObjectManagerRegistry
     * 
     * @DI\InjectParams({
     *     "managerRegistry" = @DI\Inject("doctrine"),
     * })
     * 
     * @param Doctrine\Common\Persistence\ManagerRegistry  $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managers = array();
        $this->doctrineManagerRegistry = $managerRegistry;
    }

    /**
     * Get manager for class
     * 
     * @return Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManager
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
            $manager = $managers[$key];
            
            print get_class($manager) . " manages " . $class . "\n";
            
            return $manager;
        }
        
        return $this->doctrineManagerRegistry->getManagerForClass($class);
    }
    
    /**
     * Add ObjectManager to the registry
     * 
     * @param Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManager $manager
     */
    public function addManager(ObjectManager $manager)
    {
        $this->managers[] = $manager;
    }
}
