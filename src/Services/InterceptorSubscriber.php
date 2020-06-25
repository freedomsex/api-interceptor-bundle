<?php


namespace FreedomSex\ApiInterceptorBundle;


use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use FreedomSex\ApiInterceptorBundle\Services\InterceptorDriver;
use FreedomSex\ApiInterceptorBundle\Services\RequestResourceExtractor;
use FreedomSex\PhotoUploadBundle\Services\Naming\PathInverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Liip\ImagineBundle\Service\FilterService;

class InterceptorSubscriber implements EventSubscriberInterface
{
    public function __construct(
        RequestResourceExtractor $resourceExtractor,
        InterceptorDriver $driver
    ) {
        $this->resourceExtractor = $resourceExtractor;
        $this->driver = $driver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['readIntercept', EventPriorities::PRE_VALIDATE],
            KernelEvents::VIEW => ['writeIntercept', EventPriorities::PRE_SERIALIZE],
            KernelEvents::REQUEST => ['postReadIntercept', EventPriorities::POST_READ],
            KernelEvents::REQUEST => ['preReadIntercept', EventPriorities::PRE_READ],
        ];
    }

    public function handleEvent($event, $level)
    {
        $resource = $this->resourceExtractor->extract($event->getRequest());
        if (!$resource->valid()) {
            return;
        }
        $this->driver->handle($level, $resource, $event);
    }

    public function handleResults($event)
    {
        $objects = $this->resourceExtractor->getResults($event);
        if (!$objects) {
            return;
        }
    }

    public function readIntercept(ViewEvent $event): void
    {
        $this->handleEvent($event, 'read');
    }

    public function postReadIntercept(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $this->handleEvent($event, 'postRead');
    }

    public function preReadIntercept(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $context = [];
        $context['groups'][] = 'admin:get';
        $context['groups'][] = 'admin:create';
        $this->request->attributes->set("_api_serializer_context", $context);

        $this->handleEvent($event, 'postRead');
    }

    public function writeIntercept(ViewEvent $event): void
    {
        $this->handleEvent($event, 'write');
    }

}
