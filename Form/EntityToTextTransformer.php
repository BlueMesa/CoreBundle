<?php

/*
 * This file is part of the BluemesaCoreBundle.
 *
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Form;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;


/**
 * Transforms entity into its string representation
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EntityToTextTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var ClassMetadata
     */
    protected $class;

    /**
     * @var PropertyPath
     */
    protected $propertyPath;

    /**
     * @var string
     */
    protected $format;

    /**
     * Construct EntityToTextTransformer
     *
     * @param ObjectManager $om        The object manager to use
     * @param string        $class     Class of the entity
     * @param string        $property  Property to lookup
     * @param string        $format    sprintf-compatible format string
     */
    public function __construct(ObjectManager $om, $class, $property = null, $format = null)
    {
        $this->om = $om;
        $this->class = $class;
        $this->propertyPath = (null !== $property) ? new PropertyPath($property) : null;
        $this->format = $format;
    }

    /**
     * Transform entity into string value
     *
     * @param  object  $entity
     * @return string
     */
    public function transform($entity)
    {

        if (null === $entity || '' === $entity) {
            return '';
        }

        if (!is_object($entity)) {
            throw new UnexpectedTypeException($entity, 'object');
        }

        if (null !== $this->propertyPath) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $value = (string) ($propertyAccessor->getValue($entity, $this->propertyPath));
        } else {
            $value = (string) ($entity);
        }

        if (null === $this->format) {

            return $value;
        } else {
            
            return sprintf($this->format, $value);
        }
    }

    /**
     * Transform string into entity
     *
     * @param  mixed   $key
     * @return object
     * @throws TransformationFailedException
     */
    public function reverseTransform($key)
    {
        if ('' === $key || null === $key || 'null' === $key) {
            return null;
        }

        if (!is_string($key)) {
            return null;
        }

        $property = (string) $this->propertyPath;
        if ($property) {
            $entity = $this->om->getRepository($this->class)->findOneBy(array($property => $key));
        } else {
            $entity = $this->om->getRepository($this->class)->findOneBy(array('id' => $key));
        }
        if ($entity === null) {
            throw new TransformationFailedException(sprintf('The entity with key "%s" could not be found', $key));
        }

        return $entity;
    }
}
