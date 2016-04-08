<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\DependencyInjection;

use JMS\DiExtraBundle\Annotation as DI;
use Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManager;


/**
 * ObjectManagerAwareTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait ObjectManagerAwareTrait {
    
    /**
     * @var ObjectManager $objectManager
     */
    protected $objectManager;
    
    
    /**
     * Set the Object manager service
     * 
     * @DI\InjectParams({ "objectManager" = @DI\Inject("bluemesa.core.doctrine.manager") })
     * 
     * @param ObjectManager  $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }
    
    /**
     * Get the Object manager service
     * 
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->objectManager;
    }
}
