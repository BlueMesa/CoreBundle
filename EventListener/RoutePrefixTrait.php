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

trait RoutePrefixTrait
{
    /**
     * @param  Request $request
     * @return string
     */
    private function getPrefix(Request $request)
    {
        $route = $request->get('_route');
        $prefix = $request->get('_prefix');
        if (null === $prefix) {
            $prefix = substr($route, 0, strrpos($route, '_') + 1);
        }

        return $prefix;
    }
}
