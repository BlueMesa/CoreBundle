<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\CoreBundle\Repository;


use Bluemesa\Bundle\CoreBundle\Filter\FilterInterface;

trait FilteredRepositoryTrait
{
    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * {@inheritdoc}
     */
    public function setFilter(FilterInterface $filter)
    {
        $this->filter = $filter;
    }
}
