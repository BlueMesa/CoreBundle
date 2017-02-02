<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Entity;


/**
 * MutableIdEntityInterface interface
 *
 * This interface marks entity as mutable ID entity. Such entities use doctrine ID generator by default, however users
 * are allowed to specify the ID by hand as well. In such case, you should implement a mechanism to temporarily disable
 * id generation in the entity's metadata.
 *
 * You can use the following code as a template for your own implementations:
 *
 * ```php
 * if ($entity instanceof MutableIdEntityInterface) {
 *     $metadata = $em->getClassMetadata(get_class($entity));
 *     $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
 *     $metadata->setIdGenerator(new AssignedGenerator());
 * }
 * ```
 *
 * @see \Bluemesa\Bundle\CrudBundle\EventListener\CrudMutableIdListener  Autogenerator disabler implementation example.
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
interface MutableIdEntityInterface extends EntityInterface
{
    /**
     * Set entity id
     *
     * @param integer
     */
    public function setId($id);
}
