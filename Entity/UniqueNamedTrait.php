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
 * Named trait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait UniqueNamedTrait
{
    use BaseNamedTrait;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Name must be specified")
     *
     * @var string
     */
    protected $name;
}
