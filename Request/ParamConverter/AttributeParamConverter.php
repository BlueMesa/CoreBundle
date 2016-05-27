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

use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * AttributeParamConverter
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * @DI\Service("bluemesa.converter.attribute")
 * @DI\Tag("request.param_converter", attributes = {"converter"="bluemesa_core.attribute"})
 */
class AttributeParamConverter implements ParamConverterInterface
{
    public function supports(ParamConverter $configuration)
    {
        if (!$configuration->getClass()) {

            return false;
        }

        return true;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getClass();
        $options = $this->getOptions($configuration);
        $accessor = PropertyAccess::createPropertyAccessor();

        if (! empty($options['fields'])) {
            $fields = $options['fields'];
        } else {
            $fields = $request->attributes->keys();
        }

        try {
            $object = new $class();
            foreach ($fields as $field) {
                $value = $request->attributes->get($field);
                $accessor->setValue($object, $field, $value);
            }
        } catch (\Exception $e) {
            throw new NotFoundHttpException(sprintf('Could not deserialize request attributes to object of type "%s"',
                $class));
        }

        $request->attributes->set($configuration->getName(), $object);
    }

    protected function getOptions(ParamConverter $configuration)
    {
        return array_replace(array(
            'fields' => array()
        ), $configuration->getOptions());
    }
}
