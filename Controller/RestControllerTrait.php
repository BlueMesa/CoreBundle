<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Controller;

use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for controllers
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait RestControllerTrait
{
    use ControllerTrait;

    /**
     * Get the ViewHandler.
     *
     * @return ViewHandlerInterface
     */
    protected function getViewHandler()
    {
        if (!((isset($this->container))&&($this->container instanceof ContainerInterface))) {
            throw new \LogicException("This trait may only be used inside Controllers");
        }

        if (!$this->viewhandler instanceof ViewHandlerInterface) {
            $this->viewhandler = $this->container->get('fos_rest.view_handler');
        }

        return $this->viewhandler;
    }
}
