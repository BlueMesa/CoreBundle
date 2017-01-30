<?php

/*
 * This file is part of the CoreBundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\CoreBundle\Event;


interface EntityEventInterface
{
    /**
     * @return mixed
     */
    public function getEntities();

    /**
     * @param $entities
     */
    public function setEntities($entities);
}
