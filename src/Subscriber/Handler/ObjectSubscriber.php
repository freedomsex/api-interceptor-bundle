<?php


namespace FreedomSex\ApiInterceptorBundle\Subscriber\Handler;


use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use FreedomSex\ApiInterceptorBundle\Services\Handler\PreSerializeCollector;
use FreedomSex\ApiInterceptorBundle\Services\RequestResourceExtractor;
use FreedomSex\PhotoUploadBundle\Services\Naming\PathInverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Liip\ImagineBundle\Service\FilterService;

class ObjectSubscriber implements EventSubscriberInterface
{
    public function __construct(
        RequestResourceExtractor $resourceExtractor
    ) {
        $this->resourceExtractor = $resourceExtractor;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['handle', EventPriorities::POST_READ],
        ];
    }

    public function handle(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }

//        dump($event);
//        $objects = $this->resourceExtractor->getResults($event);
//        if (!$objects) {
//            return;
//        }
//        $resource = $this->resourceExtractor->extract($request);
//        if (!$resource->valid()) {
//            return;
//        }
//        if (!is_iterable($objects)) {
//            $objects = [$objects];
//        }
//        foreach ($objects as $object) {
//            $this->handlerCollector->handle($object, $request, $event);
//        }
    }

}
