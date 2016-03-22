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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use Bluemesa\Bundle\CoreBundle\Filter\RedirectFilterInterface;

/**
 * Base class for CRUD operations
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class CRUDController extends AbstractController
{
    const ENTITY_CLASS = null;
    const ENTITY_NAME = 'entity|entities';

    /**
     * {@inheritdoc}
     */
    protected function getObjectManager($object = null)
    {
        $object = (null == $object) ? $this->getEntityClass() : $object;
        
        return parent::getObjectManager($object);
    }
    
    /**
     * List entities
     *
     * @Route("/")
     * @Route("/list/{filter}")
     * @Template()
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $filter = $this->getFilter($request);       
        if (($filter instanceof RedirectFilterInterface)&&($filter->needRedirect())) {
            
            return $this->getFilterRedirect($request, $filter);
        }
        
        $paginator  = $this->getPaginator();
        $page = $this->getCurrentPage($request);
        $repository = $this->getObjectManager()->getRepository($this->getEntityClass());
        $count = $repository->getListCount($filter);
        $query = $repository->getListQuery($filter)->setHint('knp_paginator.count', $count);
        $entities = $paginator->paginate($query, $page, 25, array('distinct' => false));
        
        return array('entities' => $entities, 'filter' => $filter);
    }

    /**
     * Show entity
     *
     * @Route("/show/{id}")
     * @Template()
     *
     * @param mixed $id
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        return array('entity' => $entity);
    }

    /**
     * Create entity
     *
     * @Route("/new")
     * @Template()
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $om = $this->getObjectManager();
        $class = $this->getEntityClass();
        $entity = new $class();
        $form = $this->createForm($this->getCreateForm(), $entity);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $om->persist($entity);
                $om->flush();
                $message = ucfirst($this->getEntityName()) . ' ' . $entity . ' was created.';
                $this->addSessionFlash('success', $message);
                $route = str_replace("_create", "_show", $request->attributes->get('_route'));
                $url = $this->generateUrl($route, array('id' => $entity->getId()));

                return $this->redirect($url);
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Edit entity
     *
     * @Route("/edit/{id}")
     * @Template()
     *
     * @param  mixed                                     $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $om = $this->getObjectManager();
        $entity = $this->getEntity($id);
        $form = $this->createForm($this->getEditForm(), $entity);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $om->persist($entity);
                $om->flush();
                $message = 'Changes to ' . $this->getEntityName() . ' ' . $entity . ' were saved.';
                $this->addSessionFlash('success', $message);
                $route = str_replace("_edit", "_show", $request->attributes->get('_route'));
                $url = $this->generateUrl($route, array('id' => $entity->getId()));

                return $this->redirect($url);
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Delete entity
     *
     * @Route("/delete/{id}")
     * @Template()
     *
     * @param  mixed                                     $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        $om = $this->getObjectManager();
        $entity = $this->getEntity($id);
        if ($request->getMethod() == 'POST') {
            $om->remove($entity);
            $om->flush();
            $message = ucfirst($this->getEntityName()) . ' ' . $entity . ' was permanently deleted.';
            $this->addSessionFlash('success', $message);
            $route = str_replace("_delete", "_list", $request->attributes->get('_route'));
            try {
                $url = $this->generateUrl($route);
                
                return $this->redirect($url);
                
            } catch (RouteNotFoundException $e) {
                
                return $this->redirect('default');
            }
        }

        return array('entity' => $entity);
    }
    
    /**
     * Get entity
     *
     * @param  mixed                                                         $id
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Bluemesa\Bundle\CoreBundle\Entity\Entity
     */
    protected function getEntity($id)
    {
        $class = $this->getEntityClass();
        if ($id instanceof $class) {
            return $id;
        }
        $om = $this->getObjectManager();
                
        try {
            $entity = $om->find($class, $id);
            if ($entity instanceof $class) {
                return $entity;
            }
            throw new NotFoundHttpException();
        } catch (NoResultException $e) {
            throw new NotFoundHttpException();
        }

        return null;
    }

    /**
     * @param  Symfony\Component\HttpFoundation\Request                   $request
     * @param  Bluemesa\Bundle\CoreBundle\Filter\RedirectFilterInterface  $filter
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function getFilterRedirect(Request $request, RedirectFilterInterface $filter)
    {
        return $this->createNotFoundException();
    }
    
    /**
     * Get filter
     *
     * @return Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface
     */
    protected function getFilter(Request $request)
    {
        return null;
    }
    
    /**
     * Get create form
     *
     * @return Symfony\Component\Form\AbstractType
     */
    protected function getCreateForm()
    {
        return $this->getEditForm();
    }

    /**
     * Get edit form
     *
     * @return Symfony\Component\Form\AbstractType
     */
    protected function getEditForm()
    {
        return null;
    }

    /**
     * Get managed entity class
     *
     * @return string
     */
    protected function getEntityClass()
    {
        return static::ENTITY_CLASS;
    }

    /**
     * Get managed entity name
     *
     * @return string
     */
    protected function getEntityName()
    {
        $names = explode('|', static::ENTITY_NAME);

        return $names[0];
    }

    /**
     * Get managed entity plural name
     *
     * @return string
     */
    protected function getEntityPluralName()
    {
        $names = explode('|', static::ENTITY_NAME);

        return $names[1];
    }

    /**
     * Check if this is a valid controller for specified entity
     *
     * @param  object  $entity
     * @return boolean
     */
    protected function controls($entity)
    {
        $reflectionClass = new \ReflectionClass($entity);

        return $this->getEntityClass() == $reflectionClass->getName();
    }
}
