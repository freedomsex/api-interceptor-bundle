<?php


namespace FreedomSex\ApiInterceptorBundle\Services;


use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Doctrine\Common\Annotations\Reader;
use FreedomSex\ApiInterceptorBundle\Annotation\Intercept;
use FreedomSex\ApiInterceptorBundle\Annotation\Interceptor;
use FreedomSex\ApiInterceptorBundle\Services\InterceptorHandler;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class InterceptorDriver
{

    public function __construct(Reader $annotationReader, InterceptorHandler $interceptorHandler)
    {
        $this->annotationReader = $annotationReader;
        $this->interceptorHandler = $interceptorHandler;
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

    public function handle($level, RequestResourceExtractor $resource, $event)
    {
        $resourceAnnotation = $this->getClassAnnotation($resource->resourceClassName());
        if (!$resourceAnnotation) {
            return;
        }
        $classAnnotation = $this->getReflection($resourceAnnotation->source);
        if (!$classAnnotation) {
            return;
        }
        $methods = $classAnnotation->getMethods();
        foreach ($methods as $method) {
            $this->run($level, $method, $resource, $event);
        }
    }

    public function run($level, $method, $resource, $event)
    {
        $methodAnnotations = $this->getMethodAnnotation($method->class, $method->name);
        if (!$methodAnnotations) {
            return;
        }
        $this->interceptorHandler->setup($event, $resource);
        $this->interceptorHandler->handle($level, $methodAnnotations, $method);
//        $methodAnnotations;
    }


}