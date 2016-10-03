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


use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Query;

interface EntityRepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder();

    /**
     * @return Query
     */
    public function createIndexQuery();

    /**
     * @return array
     */
    public function getIndexResult();

    /**
     * @return QueryBuilder
     */
    public function createCountQueryBuilder();

    /**
     * @return Query
     */
    public function createCountQuery();

    /**
     * @return array
     */
    public function getIndexCount();
}
