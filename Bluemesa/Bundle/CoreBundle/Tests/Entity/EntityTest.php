<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Tests\Entity;

use Bluemesa\Bundle\CoreBundle\Entity\Entity;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    public function testGetId()
    {
        $entity = new FakeEntity();
        $this->assertEquals(1,$entity->getId());
    }
}

class FakeEntity extends Entity
{
    public function __construct()
    {
        $this->id = 1;
    }
}
