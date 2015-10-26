<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Listener;

use JMS\DiExtraBundle\Annotation as DI;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManager;
use Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManagerRegistry;
use Bluemesa\Bundle\CoreBundle\Entity\SecuredEntityInterface;

/**
 * Description of DoctrineAclListener
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\DoctrineListener(
 *     events = {"preRemove", "postPersist"}
 * )
 */
class AclDoctrineListener {
    
    /**
     * @var Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManagerRegistry
     */
    protected $registry;
    
    
    /**
     * Construct AclDoctrineListener
     * 
     * @DI\InjectParams({
     *     "registry" = @DI\Inject("bluemesa_core.doctrine.registry"),
     * })
     * 
     * @param Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManagerRegistry  $registry
     */
    public function __construct(ObjectManagerRegistry $registry)
    {
        $this->registry = $registry;
    }
    
    /**
     * 
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        
        if ($object instanceof SecuredEntityInterface) {
            $om = $this->registry->getManagerForClass($object);
            if (($om instanceof ObjectManager)&&($om->isAutoAclEnabled())) {
                $om->removeACL($object);
            }
        }
    }
    
    /**
     * 
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof SecuredEntityInterface) {
            $om = $this->registry->getManagerForClass($object);
            if (($om instanceof ObjectManager)&&($om->isAutoAclEnabled())) {
                $om->createACL($object);
            }
        }
    }
}
