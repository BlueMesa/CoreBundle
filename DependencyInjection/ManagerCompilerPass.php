<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


/**
 * Register BlueMesa Object Manager
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ManagerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('bluemesa.core.doctrine.registry')) {
            return;
        }
        
        $definition = $container->getDefinition(
            'bluemesa.core.doctrine.registry'
        );
        
        $taggedServices = $container->findTaggedServiceIds(
            'bluemesa_core.object_manager'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addManager',
                array(new Reference($id))
            );
        }
    }
}
