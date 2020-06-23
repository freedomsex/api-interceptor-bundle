<?php


namespace FreedomSex\ApiInterceptorBundle\Subscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use FreedomSex\ApiInterceptorBundle\Annotation\Reader;
use FreedomSex\ApiInterceptorBundle\Services\AnnotationDriver;
use FreedomSex\ApiInterceptorBundle\Services\Reader\ReadRunner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ReaderSubscriber implements EventSubscriberInterface
{
    public $runner;

    public function __construct(AnnotationDriver $driver)
    {
        $this->driver = $driver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['handle', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function handle(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        if ($controllerResult instanceof Response) {
            return;
        }

        $attributes = RequestAttributesExtractor::extractAttributes($request);
        $className = $this->resourceClassName($attributes);

        if (!$attributes || !$className) {
            return;
        }
//        !\is_a($attributes['resource_class'], MediaObject::class, true)

        if ($className) {
            $this->driver->setClassName($className);
            $this->driver->setAttributes($attributes);
            $this->driver->handle($event, Reader::class);
        }
    }

    public function resourceClassName($attributes)
    {
        return $attributes['resource_class'] ?? null;
    }
}
