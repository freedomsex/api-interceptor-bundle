<?php


namespace FreedomSex\ApiInterceptorBundle\Subscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use FreedomSex\ApiInterceptorBundle\Services\Handler\PreSerializeCollector;
use FreedomSex\ApiInterceptorBundle\Services\RequestResourceExtractor;
use FreedomSex\PhotoUploadBundle\Services\Naming\PathInverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Liip\ImagineBundle\Service\FilterService;

class PreSerializeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        RequestResourceExtractor $resourceExtractor,
        PreSerializeCollector $handlerCollector
    ) {
        $this->resourceExtractor = $resourceExtractor;
        $this->handlerCollector = $handlerCollector;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['handle', EventPriorities::PRE_SERIALIZE],
        ];
    }

    public function handle(ViewEvent $event): void
    {
        $request = $event->getRequest();
        $objects = $this->resourceExtractor->getResults($event);
        if (!$objects) {
            return;
        }
        $resource = $this->resourceExtractor->extract($request);
        if (!$resource->valid()) {
            return;
        }
        if (!is_iterable($objects)) {
            $objects = [$objects];
        }
        foreach ($objects as $object) {
            $this->handlerCollector->handle($object, $request, $event);
        }
    }

}
