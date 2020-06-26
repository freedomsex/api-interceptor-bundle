<?php


namespace FreedomSex\ApiInterceptorBundle\Services;

use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Doctrine\Common\Annotations\Reader;
use FreedomSex\ApiInterceptorBundle\Annotation\Intercept;
use FreedomSex\ApiInterceptorBundle\Annotation\Interceptor;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class RequestHandlerDriver
{
    protected $handlers = [];

    public function __construct(
        Reader $annotationReader,
        InterceptorHandler $interceptorHandler
    ) {
        $this->annotationReader = $annotationReader;
        $this->interceptorHandler = $interceptorHandler;
    }

    public function addHandler($handler)
    {
        $this->handlers[] = $handler;
    }

    public function getReflection($className): \ReflectionClass
    {
        try {
            $reflection = new ReflectionClass($className);
        } catch (ReflectionException $e) {
            throw new RuntimeException('Failed to read ReflectionClass! ' . $className);
        }
        return $reflection;
    }

    public function getClassAnnotation($resourceClassName)
    {
        return $this->annotationReader->getClassAnnotation(
            $this->getReflection($resourceClassName),
            Interceptor::class);
    }

    public function getMethodAnnotation($className, $methodName)
    {
        $reflection = new \ReflectionMethod($className, $methodName);
        return $this->annotationReader->getMethodAnnotation($reflection, Intercept::class);
    }

    public function handle($level, $event)
    {
        $this->interceptorHandler->setup($event);
        foreach ($this->handlers as $handlerClassName) {
            $classAnnotation = $this->getReflection($handlerClassName);
            if (!$classAnnotation) {
                return;
            }
            $methods = $classAnnotation->getMethods();
            foreach ($methods as $method) {
                $this->run($level, $method, $event);
            }
        }
    }

    public function run($level, $method)
    {
        $interceptor = $this->getMethodAnnotation($method->class, $method->name);
        if (!$interceptor) {
            return;
        }
        $this->interceptorHandler->handle($level, $interceptor, $method);
//        $methodAnnotations;
    }

}
