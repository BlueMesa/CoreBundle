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

use Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManager;
use InvalidArgumentException;
use Knp\Component\Pager\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @param  object|string  $object
     * @return ObjectManager
     */
    protected function getObjectManager($object = null)
    {
        return $this->get('bluemesa.core.doctrine.registry')->getManagerForClass($object);
    }

    /**
     * Get session
     *
     * @return SessionInterface
     */
    protected function getSession()
    {
        return $this->get('session');
    }

    /**
     * Get paginator
     *
     * @return Paginator
     */
    protected function getPaginator()
    {
        return $this->get('knp_paginator');
    }

    /**
     * Get current page
     *
     * @param  Request  $request
     * @return integer
     */
    protected function getCurrentPage(Request $request)
    {
        return $request->query->get('page', 1);
    }
    
    /**
     * Adds a flash message for type
     *
     * @param  string                    $type
     * @param  string                    $message
     * @throws InvalidArgumentException
     */
    protected function addSessionFlash($type, $message)
    {
        $session = $this->getSession();
        if ($session instanceof Session) {
            $session->getFlashBag()->add($type, $message);
        } else {
            throw new InvalidArgumentException("Session should be an instance of Symfony\\Component\\HttpFoundation\\Session\\Session");
        }

    }
}
