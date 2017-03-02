<?php

/*
 * This file is part of the CoreBundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\CoreBundle\Utils;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

trait RouteExistsTrait
{
    /**
     * @param  string  $route
     * @return boolean
     */
    private function routeExists($route)
    {
        if (! $this->router instanceof RouterInterface) {
            throw new \LogicException(
                "Calling class must have a router property set to an instance of RouterInterface");
        }

        try {
            $this->router->generate($route);
        } catch (\Exception $e) {
            if ($e instanceof RouteNotFoundException) {
                return false;
            }
        }

        return true;
    }
}
