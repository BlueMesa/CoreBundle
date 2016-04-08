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

use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;


/**
 * This controller generates choices list for EntityTypeaheadType
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AJAXController extends Controller
{
    /**
     * Search for the specified entity by its property
     *
     * @Route("/_ajax/choices/{class}/{property}", name="BluemesaCoreBundle_ajax_choices")
     *
     * @param  Request   $request
     * @param  string    $class     Entity class to search for
     * @param  string    $property  Property to lookup in search
     * @return Response
     * @throws InvalidArgumentException
     */
    public function choicesAction(Request $request, $class, $property)
    {
        $query = $request->query->get('query');

        $repository = $this->getDoctrine()->getRepository($class);
        if (!$repository instanceof EntityRepository) {
            throw new InvalidArgumentException();
        }
        $qb = $repository->createQueryBuilder('b');

        $terms = explode(" ",$query);
        foreach ($terms as $term) {
            $qb = $qb->andWhere("b." . $property . " like '%" . $term . "%'");
        }
        $found = $qb->getQuery()->getResult();

        $propertyPath = (null !== $property) ? new PropertyPath($property) : null;
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        
        $options = array();
        foreach ($found as $entity) {
            if (null !== $propertyPath) {
                $options[] = (string) ($propertyAccessor->getValue($entity, $propertyPath));
            } else {
                $options[] = (string) ($entity);
            }
        }

        $response = new JsonResponse();
        $response->setData($options);

        return $response;
    }
}
