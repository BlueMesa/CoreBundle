<?php

/*
 * This file is part of the Core Bundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\CoreBundle\Request;


use Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManagerRegistry;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;


/**
 * Class Abstract Handler
 *
 * @package Bluemesa\Bundle\CrudBundle\Request
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class AbstractHandler
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var ObjectManagerRegistry
     */
    protected $registry;

    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @var RouterInterface
     */
    protected $router;


    /**
     * CrudHandler constructor.
     *
     * @DI\InjectParams({
     *     "dispatcher" = @DI\Inject("event_dispatcher"),
     *     "registry" = @DI\Inject("bluemesa.core.doctrine.registry"),
     *     "factory" = @DI\Inject("form.factory"),
     *     "router" = @DI\Inject("router")
     * })
     *
     * @param EventDispatcherInterface  $dispatcher
     * @param ObjectManagerRegistry     $registry
     * @param FormFactoryInterface      $factory
     * @param RouterInterface           $router
     */
    public function __construct(EventDispatcherInterface $dispatcher,
                                ObjectManagerRegistry $registry,
                                FormFactoryInterface $factory,
                                RouterInterface $router)
    {
        $this->dispatcher = $dispatcher;
        $this->registry = $registry;
        $this->factory = $factory;
        $this->router = $router;
    }

    /**
     * This method calls a proper handler for the incoming request
     *
     * @param Request $request
     *
     * @return View
     */
    abstract public function handle(Request $request);
}
