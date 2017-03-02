<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\CoreBundle\EventListener;


use Symfony\Component\HttpFoundation\Request;

trait AttributeGeneratorTrait
{
    /**
     * @param Request $request
     * @param string  $attribute
     * @param string  $value
     */
    private function addRequestAttribute(Request $request, $attribute, $value)
    {
        if ((null !== $value)&&(! $request->attributes->has($attribute))) {
            $request->attributes->set($attribute, $value);
        }
    }
}
