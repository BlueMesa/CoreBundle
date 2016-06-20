<?php

/*
 * This file is part of the Bluemesa SensorBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\CoreBundle\Entity;


class DatePeriod
{
    const LastDay = 24;
    const LastWeek = 168;
    const LastMonth = 720;

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @var int
     */
    private $period;


    public function __construct()
    {
        $this->period = null;
        $this->start = null;
        $this->end = null;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        if ((null === $this->end)&&(null !== $this->period)) {
            $now = new \DateTime();

            return $now->sub(new \DateInterval('PT' . $this->period . 'H'));
        }

        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start = null)
    {
        $this->start = $start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        if ((null === $this->end)&&(null !== $this->period)) {

            return new \DateTime();
        }

        return $this->end;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end = null)
    {
        $this->end = $end;
    }

    /**
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param int $period
     *
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }
}
