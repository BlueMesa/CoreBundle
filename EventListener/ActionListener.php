<?php

/*
 * This file is part of the CRUD Bundle.
 *
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\EventListener;

use Bluemesa\Bundle\CoreBundle\Controller\Annotations\Action;
use Bluemesa\Bundle\CoreBundle\Utils\RouteExistsTrait;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

/**
 * The CrudAnnotationListener handles CRUD annotations for controllers.
 *
 * @DI\Service("bluemesa.core.listener.annotation")
 * @DI\Tag("kernel.event_listener",
 *     attributes = {
 *         "event" = "kernel.controller",
 *         "method" = "onKernelController",
 *         "priority" = 10
 *     }
 * )
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ActionListener
{
    use AttributeGeneratorTrait;
    use RouteExistsTrait;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * Constructor.
     *
     * @DI\InjectParams({
     *     "reader" = @DI\Inject("annotation_reader"),
     *     "router" = @DI\Inject("router")
     * })
     *
     * @param Reader                $reader   A Reader instance
     * @param RouterInterface       $router   A RouterInterface instance
     */
    public function __construct(Reader $reader, RouterInterface $router)
    {
        $this->reader = $reader;
        $this->router = $router;
    }

    /**
     * Adds CRUD parameters to the Request object.
     *
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();

        if (is_array($controller)) {
            $c = new \ReflectionClass(ClassUtils::getClass($controller[0]));
            $m = new \ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && is_callable($controller, '__invoke')) {
            /** @var object $controller */
            $c = new \ReflectionClass(ClassUtils::getClass($controller));
            $m = new \ReflectionMethod($controller, '__invoke');
        } else {
            return;
        }

        $actionAnnotation = null;
        foreach ($this->reader->getMethodAnnotations($m) as $annotation) {
            if ($annotation instanceof Action) {
                $actionAnnotation = $annotation;
            }
        }
        if (! $actionAnnotation) {
            return;
        }

        $action = $this->getActionName($actionAnnotation, $m);
        $redirect = $this->getRedirect($actionAnnotation);
        $prefix = $this->getRoutePrefix($c);
        $referer = $this->getReferrer($request);
        $this->addRequestAttribute($request, 'action', $action);
        $this->addRequestAttribute($request, 'redirect', $redirect);
        $this->addRequestAttribute($request, '_prefix', $prefix);
        $this->addRequestAttribute($request, '_referer', $referer);
    }

    /**
     * @param Action $actionAnnotation
     * @param \ReflectionMethod $m
     *
     * @return string
     * @throws \LogicException
     */
    private function getActionName(Action $actionAnnotation, \ReflectionMethod $m)
    {
        $action = $actionAnnotation->getAction();
        if (null === $action) {
            $method = $m->getName();
            $action = str_replace("Action", "", $method);
        }

        return $action;
    }

    /**
     * @param Action $actionAnnotation
     *
     * @return string
     */
    private function getRedirect($actionAnnotation)
    {
        $route = $actionAnnotation->getRedirectRoute();

        return $this->routeExists($route) ? $route : null;
    }

    /**
     * @param \ReflectionClass $c
     *
     * @return string
     */
    private function getRoutePrefix(\ReflectionClass $c)
    {
        /** @var NamePrefix $namePrefixAnnotation */
        $namePrefixAnnotation = $this->reader->getClassAnnotation($c, NamePrefix::class);

        return $namePrefixAnnotation->value;
    }


    private function getReferrer(Request $request)
    {
        if ($this->router instanceof Router) {
            $referer = $request->server->get('HTTP_REFERER');
            $path = substr($referer, strpos($referer, $request->getBaseUrl()));
            $path = str_replace($request->getBaseUrl(), '', $path);
            $matcher = $this->router->getMatcher();
            try {
                $parameters = $matcher->match($path);
            } catch (ExceptionInterface $e) {

                return null;
            }

            $route = $parameters['_route'];
            unset($parameters['_route']);
            unset($parameters['_controller']);

            return array('route' => $route, 'parameters' => $parameters);
        }

        return null;
    }
}
