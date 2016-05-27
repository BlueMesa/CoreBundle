<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter as SensioDoctrineParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * DoctrineParamConverter
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\Service("bluemesa.converter.doctrine")
 * @DI\Tag("request.param_converter", attributes = {"priority"=2, "converter"="bluemesa.converter.doctrine"})
 */
class DoctrineParamConverter extends SensioDoctrineParamConverter
{
    /**
     * @DI\InjectParams({"registry" = @DI\Inject("doctrine")})
     * 
     * @param ManagerRegistry  $registry
     */
    public function __construct(ManagerRegistry $registry = null)
    {
        parent::__construct($registry);
    }
    
    /**
     * Apply the converter
     *
     * @param  Request                $request
     * @param  ParamConverter         $configuration
     * @return boolean
     * @throws NotFoundHttpException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name    = $configuration->getName();
        $options = $this->getOptions($configuration);
        $id      = $this->getIdentifier($request, $options, $name);

        if (is_array($id)) {
            $id = implode (":",$id);
        }

        try {
            return parent::apply($request, $configuration);
            
        } catch (NotFoundHttpException $exeption) {
            
            throw new NotFoundHttpException(sprintf($options['error_message'],$id));
        }
    }

    /**
     * Get options
     *
     * @param  ParamConverter  $configuration
     * @return array
     */
    protected function getOptions(ParamConverter $configuration)
    {
        return array_replace(array(
            'error_message'  => 'Not Found'
        ), parent::getOptions($configuration));
    }
}
