<?php


namespace FreedomSex\ApiInterceptorBundle\EventListener;


use ApiPlatform\Core\Util\RequestAttributesExtractor;
use FreedomSex\ApiInterceptorBundle\Annotation\Interceptor;
use FreedomSex\ApiInterceptorBundle\Services\AnnotationDriver;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class InterceptorListener
{
    public $runner;

    public function __construct(AnnotationDriver $driver)
    {
        $this->driver = $driver;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMasterRequest() or !$event->getController()) {
            return;
        }
        $attributes = RequestAttributesExtractor::extractAttributes($event->getRequest());
        $className = $this->resourceClassName($attributes);
        if ($className) {
            $this->driver->setClassName($className);
            $this->driver->setAttributes($attributes);
            $this->driver->handle($event, Interceptor::class);
        }
    }

    public function resourceClassName($attributes)
    {
        return $attributes['resource_class'] ?? null;
    }
}
