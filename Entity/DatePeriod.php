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


    public function __construct()
    {
        $this->setPeriod(self::LastDay);
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start)
    {
        $this->start = $start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end)
    {
        $this->end = $end;
    }

    /**
     * @return string
     */
    public function getPeriod()
    {
        return null;
    }

    /**
     * @param int $period
     */
    public function setPeriod($period)
    {
        $now = new \DateTime();
        $this->end = clone $now;
        $this->start = $now->sub(new \DateInterval('PT' . $period . 'H'));
    }
}
