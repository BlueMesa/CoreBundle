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
 * @deprecated Use Bluemesa\Bundle\CrudBundle\Filter\RedirectFilterInterface
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
interface RedirectFilterInterface extends FilterInterface {
    
    /**
     * Is list redirect needed?
     * 
     * @return boolean
     */
    public function needRedirect();
}
