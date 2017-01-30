<?php

/*
 * This file is part of the CRUD Bundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\CoreBundle\EventListener;

use Bluemesa\Bundle\CoreBundle\Controller\Annotations\Paginate;
use Bluemesa\Bundle\CoreBundle\Entity\Entity;
use Bluemesa\Bundle\CoreBundle\Event\EntityEventInterface;
use Bluemesa\Bundle\CoreBundle\Event\RequestEventInterface;
use Doctrine\Common\Annotations\Reader;
use JMS\DiExtraBundle\Annotation as DI;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;


/**
 * The AnnotationListener handles Pagination annotation for controllers.
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class PaginationListener
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * Constructor.
     *
     * @DI\InjectParams({
     *     "reader" = @DI\Inject("annotation_reader"),
     *     "paginator" = @DI\Inject("knp_paginator"),
     * })
     *
     * @param  Reader              $reader
     * @param  PaginatorInterface  $paginator
     */
    public function __construct(Reader $reader, PaginatorInterface $paginator)
    {
        $this->reader = $reader;
        $this->paginator = $paginator;
    }

    /**
     * @param Event $event
     */
    public function onPaginate(Event $event)
    {
        if ((!$event instanceof RequestEventInterface)||(!$event instanceof EntityEventInterface)) {
            return;
        }

        $request = $event->getRequest();
        $controller = $this->getController($request);

        if (is_array($controller)) {
            $m = new \ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && is_callable($controller, '__invoke')) {
            $m = new \ReflectionMethod($controller, '__invoke');
        } else {
            return;
        }

        /** @var Paginate $paginateAnnotation */
        $paginateAnnotation = $this->reader->getMethodAnnotation($m, Paginate::class);
        if (! $paginateAnnotation) {
            return;
        }

        $maxResults = $paginateAnnotation->getMaxResults();
        $page = $request->get('page', 1);
        $target = $this->getPaginationTarget($event);
        $options = $this->getPaginationOptions($event);

        $entities = $this->paginator->paginate($target, $page, $maxResults, $options);
        $event->setEntities($entities);
    }

    /**
     * @param  Request  $request
     * @return array
     */
    private function getController($request)
    {
        return explode("::", $request->get('_controller'));
    }

    /**
     * @param  Event  $event
     * @return array
     */
    protected function getPaginationOptions(Event $event)
    {
        return array('distinct' => false);
    }

    /**
     * @param  Event  $event
     * @return mixed
     */
    abstract protected function getPaginationTarget(Event $event);
}
