<?php


namespace FreedomSex\ApiInterceptorBundle\Services;


use Doctrine\Common\Annotations\Reader;
use FreedomSex\ApiInterceptorBundle\Services\Worker\WorkerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

class AnnotationDriver
{
    private $annotationReader;

    protected $event;
    /* @var Request */
    protected $request;

    public $className;
    public $attributes;

    public function __construct(Reader $annotationReader, ContainerInterface $container)
    {
        $this->annotationReader = $annotationReader;
        $this->container = $container;
    }

    public function setClassName($className)
    {
        $this->className = $className;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getAttributes($annotation)
    {
        return $annotation->attributes ?: $this->attributes;
    }

    public function getReflection(): ReflectionClass
    {
        try {
            $reflection = new ReflectionClass($this->getClassName());
        } catch (ReflectionException $e) {
            throw new RuntimeException('Failed to read annotation! ' . $this->getClassName());
        }
        return $reflection;
    }

    public function getAnnotation($annotationClass)
    {
        return $this->annotationReader->getClassAnnotation(
            $this->getReflection(),
            $annotationClass
        );
    }

    public function getWorker($annotation)
    {
        $workerClassName = $annotation->worker;
        try {
            return $this->container->get($workerClassName);
        } catch (ServiceNotFoundException $e) {
            throw new RuntimeException('Failed to create worker ' . $workerClassName);
        }
    }

    public function handle($event, $annotationClass)
    {
        $this->event = $event;
        $this->request = $event->getRequest();
        $annotation = $this->getAnnotation($annotationClass);
        if ($annotation) {
            $this->run(
                $this->getWorker($annotation),
                $this->getAttributes($annotation)
            );
        }
    }

    public function run(WorkerInterface $worker, $attributes)
    {
        $worker->run($this->request, $attributes, $this->event);
    }
}
