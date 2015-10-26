<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Bluemesa\Bundle\CoreBundle\DependencyInjection\ManagerCompilerPass;


/**
 * BluemesaCoreBundle
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class BluemesaCoreBundle extends Bundle
{
    /**
     * Build the bundle
     * 
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ManagerCompilerPass());
    }
}
