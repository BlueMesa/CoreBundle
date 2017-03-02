<?php

/*
 * This file is part of the Core Bundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\CoreBundle\Controller\Annotations;


/**
 * Action Annotation
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Action
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $redirect;


    /**
     * Action Annotation constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->action = $values['value'];
        $this->redirect = $values['redirect'];
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getRedirectRoute()
    {
        return $this->redirect;
    }
}
