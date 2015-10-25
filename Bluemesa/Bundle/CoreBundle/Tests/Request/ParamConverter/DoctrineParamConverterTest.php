<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Tests\Request\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Bluemesa\Bundle\CoreBundle\Request\ParamConverter\DoctrineParamConverter;

class DoctrineParamConverterTest extends \PHPUnit_Framework_TestCase
{
    private $converter;
    private $registry;

    public function testApply()
    {
        $request = new Request();
        $request->attributes->set('id', 1);

        $config = $this->createConfiguration('stdClass', array('id' => 'id'), 'arg');

        $manager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->registry->expects($this->once())
              ->method('getManagerForClass')
              ->with('stdClass')
              ->will($this->returnValue($manager));

        $manager->expects($this->once())
            ->method('getRepository')
            ->with('stdClass')
            ->will($this->returnValue($objectRepository));

        $objectRepository->expects($this->once())
                      ->method('find')
                      ->with($this->equalTo(1))
                      ->will($this->returnValue($object = new \stdClass));

        $ret = $this->converter->apply($request, $config);

        $this->assertTrue($ret);
        $this->assertSame($object, $request->attributes->get('arg'));
    }

    public function testApplyNotFound()
    {
        $request = new Request();
        $request->attributes->set('id', 1);

        $config = $this->createConfiguration('stdClass', array('id' => 'id'), 'arg');

        $manager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectRepository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->registry->expects($this->once())
              ->method('getManagerForClass')
              ->with('stdClass')
              ->will($this->returnValue($manager));

        $manager->expects($this->once())
            ->method('getRepository')
            ->with('stdClass')
            ->will($this->returnValue($objectRepository));

        $objectRepository->expects($this->once())
                      ->method('find')
                      ->with($this->equalTo(1))
                      ->will($this->returnValue(null));

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $this->converter->apply($request, $config);
    }

    protected function setUp()
    {
        $this->registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $this->converter = new DoctrineParamConverter($this->registry);
    }

    private function createConfiguration($class = null, array $options = null, $name = 'arg', $isOptional = false)
    {
        $methods = array('getClass', 'getAliasName', 'getOptions', 'getName', 'allowArray');
        if (null !== $isOptional) {
            $methods[] = 'isOptional';
        }
        $config = $this
            ->getMockBuilder('Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter')
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
        if ($options !== null) {
            $config->expects($this->exactly(2))
                   ->method('getOptions')
                   ->will($this->returnValue($options));
        }
        if ($class !== null) {
            $config->expects($this->any())
                   ->method('getClass')
                   ->will($this->returnValue($class));
        }
        $config->expects($this->any())
               ->method('getName')
               ->will($this->returnValue($name));
        if (null !== $isOptional) {
            $config->expects($this->any())
                   ->method('isOptional')
                   ->will($this->returnValue($isOptional));
        }

        return $config;
    }
}
