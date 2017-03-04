<?php

/*
 * This file is part of the CoreBundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\CoreBundle\Request;


use Bluemesa\Bundle\CoreBundle\Event\ControllerEvent;
use Bluemesa\Bundle\CoreBundle\Event\ResultEventInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait FormHandlerTrait
{
    /**
     * @param  Request        $request
     * @param  mixed          $entity
     * @param  FormInterface  $form
     * @param  array          $events
     * @param  callable       $handler
     * @return mixed
     */
    private function handleFormRequest(Request $request, $entity, FormInterface $form,
                                       array $events, callable $handler)
    {
        if ((! $this instanceof AbstractHandler)||(! $this->dispatcher instanceof EventDispatcherInterface)) {
            throw new \LogicException();
        }

        $result = null;

        /** @var ControllerEvent $event */
        $event = $this->createEvent($events['class'], $request, $entity, $result, $form);
        $this->dispatcher->dispatch($events['initialize'], $event);

        if (null !== $event->getView()) {
            return $event->getView();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $this->createEvent($events['class'], $request, $entity, $result, $form);
            $this->dispatcher->dispatch($events['submitted'], $event);

            $result = $handler($request, $event);

            $event = $this->createEvent($events['class'], $request, $entity, $result, $form, $event->getView());
            $this->dispatcher->dispatch($events['success'], $event);

            if (null === $view = $event->getView()) {
                $redirect = $this->getRedirect($request, $result);
                $view = View::createRouteRedirect($redirect['route'], $redirect['parameters']);
            }

        } else {
            $view = View::create(array('entity' => $entity, 'form' => $form->createView()));
        }

        $event = $this->createEvent($events['class'], $request, $entity, $result, $form, $view);
        $this->dispatcher->dispatch($events['completed'], $event);

        return $event->getView();
    }

    /**
     * @param  string         $class
     * @param  Request        $request
     * @param  mixed          $entity
     * @param  mixed          $result
     * @param  FormInterface  $form
     * @param  View           $view
     * @return Event
     */
    protected function createEvent($class, Request $request, $entity,
                                 $result = null, FormInterface $form = null, View $view = null)
    {
        if (is_a($class, ResultEventInterface::class, true)) {
            $event = new $class($request, $entity, $result, $form, $view);
        } else {
            $event = new $class($request, $entity, $form, $view);
        }

        return $event;
    }

    /**
     * @param  Request $request
     * @param  mixed   $entity
     * @return string
     */
    protected function getRedirect(Request $request, $entity)
    {
        $route = $request->get('redirect');
        $parameters = array();

        return array('route' => $route, 'parameters' => $parameters);
    }
}
