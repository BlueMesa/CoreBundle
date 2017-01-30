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


use FOS\RestBundle\View\View;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class ControllerEvent extends Event implements RequestEventInterface, ViewEventInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var View
     */
    protected $view;


    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}
