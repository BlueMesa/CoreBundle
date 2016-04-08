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

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Entity as hidden input control
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\FormType
 */
class HiddenEntityType extends TextEntityType
{
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}
