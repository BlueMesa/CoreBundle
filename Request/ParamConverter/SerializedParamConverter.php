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
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ParamConverterInterface
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * @DI\Service("bluemesa.converter.serialized")
 * @DI\Tag("request.param_converter", attributes = {"converter"="bluemesa_core.serialized", "priority"="false"})
 */
class SerializedParamConverter implements ParamConverterInterface
{
    private $serializer;

    /**
     * @DI\InjectParams({"serializer" = @DI\Inject("serializer")})
     *
     * @param SerializerInterface  $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

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

        if ($options['format'] !== null) {
            $formats = array($options['format']);
        } else {
            $formats = array('xml', 'json', 'yaml');
        }

        $object = null;

        foreach ($formats as $format) {
            try {
                $object = $this->serializer->deserialize(
                    $request->getContent(),
                    $class,
                    $format
                );
            }
            catch (\Exception $e) {}
        }

        if (null == $object) {
            throw new NotFoundHttpException(sprintf('Could not deserialize request content to object of type "%s"',
                $class));
        }

        $request->attributes->set($configuration->getName(), $object);
    }

    protected function getOptions(ParamConverter $configuration)
    {
        return array_replace(array(
            'format' => null
        ), $configuration->getOptions());
    }
}
