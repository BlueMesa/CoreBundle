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
 * ExtendedBaseNamedTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait ExtendedBaseNamedTrait
{
    use BaseNamedTrait;
    
    /**
     * Return string representation of Entity
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}
