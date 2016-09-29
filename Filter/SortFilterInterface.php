<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Filter;

/**
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
interface SortFilterInterface extends FilterInterface {
    
    /**
     * Get sort
     *  
     * @return string
     */
    public function getSort();

    /**
     * Set sort;
     * 
     * @param string $sort
     */
    public function setSort($sort);
    
    /**
     * Get order
     *  
     * @return string
     */
    public function getOrder();

    /**
     * Set order;
     * 
     * @param string $order
     */
    public function setOrder($order);
}
