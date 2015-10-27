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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Base class for controllers
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AbstractController extends Controller
{
    /**
     * Get object manager
     *
     * @param  object|string $object
     * @return Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManager
     */
    protected function getObjectManager($object = null)
    {
        return $this->get('bluemesa.core.doctrine.registry')->getManagerForClass($object);
    }

    /**
     * Get session
     *
     * @return Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected function getSession()
    {
        return $this->get('session');
    }

    /**
     * Get paginator
     *
     * @return Knp\Component\Pager\Paginator
     */
    protected function getPaginator()
    {
        return $this->get('knp_paginator');
    }

    /**
     * Get current page
     *
     * @return integer
     */
    protected function getCurrentPage()
    {
        return $this->getRequest()->query->get('page', 1);
    }
    
    /**
     * Adds a flash message for type
     *
     * @param string $type
     * @param string $message
     */
    protected function addSessionFlash($type, $message)
    {
        $this->getSession()->getFlashBag()->add($type, $message);
    }
}
