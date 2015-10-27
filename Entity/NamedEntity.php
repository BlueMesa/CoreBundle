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

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * NamedEntity class
 *
 * @ORM\MappedSuperclass
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class NamedEntity extends Entity implements NamedEntityInterface
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Name must be specified")
     *
     * @var string
     */
    protected $name;
    
    
    /**
     * Return string representation of StorageUnit
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
    
    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
