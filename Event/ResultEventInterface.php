<?php

/*
 * This file is part of the Core Bundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\CoreBundle\Event;


use Symfony\Component\HttpFoundation\Request;

interface ResultEventInterface
{
    /**
     * @return Request
     */
    public function getResult();
}
