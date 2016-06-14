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
use Bluemesa\Bundle\CoreBundle\Entity\DatePeriod;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DatePeriodParamConverter
 * @package Bluemesa\Bundle\CoreBundle\Request\ParamConverter
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * @DI\Service("bluemesa.converter.date_period")
 * @DI\Tag("request.param_converter", attributes = {"converter"="bluemesa_core.date_period"})
 */
class DatePeriodParamConverter implements ParamConverterInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws NotFoundHttpException When invalid date given
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $options = $this->getOptions($configuration);
        $period = new DatePeriod();

        if ($request->attributes->has($options['start_attribute'])) {
            $period->setStart($this->getDate($request->attributes->get($options['start_attribute']), $options));
            if ($request->attributes->has($options['end_attribute'])) {
                $period->setEnd($this->getDate($request->attributes->get($options['end_attribute']), $options));
            }
        } elseif ($request->attributes->has($options['period_attribute'])) {
            echo "<pre>" . "Setting period to " . $request->attributes->get($options['period_attribute']) . "</pre>";
            $period->setPeriod($request->attributes->get($options['period_attribute']));
        } elseif ($configuration->isOptional()) {
            $period = null;
        } else {
            throw new NotFoundHttpException('No dates provided.');
        }

        $request->attributes->set($name, $period);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        return 'Bluemesa\Bundle\CoreBundle\Entity\DatePeriod' === $configuration->getClass();
    }

    /**
     * @param  string   $string
     * @param  array    $options
     * @return DateTime
     */
    protected function getDate($string, $options)
    {
        if (isset($options['format'])) {
            $date = DateTime::createFromFormat($options['format'], $string);
            if (!$date) {
                throw new NotFoundHttpException('Invalid date given.');
            }
        } else {
            if (false === strtotime($string)) {
                throw new NotFoundHttpException('Invalid date given.');
            }
            $date = new DateTime($string);
        }

        return $date;
    }

    /**
     * @param ParamConverter $configuration
     * @return array
     */
    protected function getOptions(ParamConverter $configuration)
    {
        return array_replace(array(
            'start_attribute' => 'start',
            'end_attribute' => 'end',
            'period_attribute' => 'period'
        ), $configuration->getOptions());
    }
}
