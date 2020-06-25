<?php


namespace FreedomSex\ApiInterceptorBundle\Services;


use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use FreedomSex\ApiInterceptorBundle\Services\InterceptorDriver;
use FreedomSex\ApiInterceptorBundle\Services\RequestResourceExtractor;
use FreedomSex\PhotoUploadBundle\Services\Naming\PathInverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
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
            KernelEvents::REQUEST => [
                ['REQUEST', 0],
                ['PRE_DESERIALIZE', EventPriorities::PRE_DESERIALIZE],
                ['POST_DESERIALIZE', EventPriorities::POST_DESERIALIZE],
                ['PRE_READ', EventPriorities::PRE_READ],
                ['POST_READ', EventPriorities::POST_READ],
            ],
            KernelEvents::VIEW => [
                ['VIEW', 0],
                ['PRE_WRITE', EventPriorities::PRE_WRITE],
                ['POST_WRITE', EventPriorities::POST_WRITE],
                ['PRE_VALIDATE', EventPriorities::PRE_VALIDATE],
                ['POST_VALIDATE', EventPriorities::POST_VALIDATE],
                ['PRE_SERIALIZE', EventPriorities::PRE_SERIALIZE],
                ['POST_SERIALIZE', EventPriorities::POST_SERIALIZE],
                ['PRE_RESPOND', EventPriorities::PRE_RESPOND],
            ],
            KernelEvents::RESPONSE => [
                ['RESPONSE', 0],
                ['POST_RESPOND', EventPriorities::POST_RESPOND],
            ],
            KernelEvents::TERMINATE => [
                ['TERMINATE', 0]
            ],
        ];
    }
    public function handle($event, $levels)
    {
        $resource = $this->resourceExtractor->extract($event->getRequest());
        if (!$resource->valid()) {
            return;
        }
        foreach ($levels as $level) {
            $this->driver->handle($level, $resource, $event);
        }
    }

    public function REQUEST($event)
    {
        $this->handle($event, ['init', 'REQUEST']);
    }

    public function VIEW($event)
    {
        $this->handle($event, ['VIEW']);
    }

    public function RESPONSE($event)
    {
        $this->handle($event, ['RESPONSE']);
    }

    public function TERMINATE($event)
    {
        $this->handle($event, ['audit', 'finish', 'TERMINATE']);
    }

//    public function handleResults($event)
//    {
//        $objects = $this->resourceExtractor->getResults($event);
//        if (!$objects) {
//            return;
//        }
//    }

    // Retrieves data from the persistence system using the data providers
    public function PRE_READ(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
//        $context = [];
//        $context['groups'][] = 'admin:get';
//        $context['groups'][] = 'admin:create';
//        $request = $event->getRequest();
//        $request->attributes->set("_api_serializer_context", $context);

        $this->handle($event, ['PRE_READ']);
    }

    public function POST_READ(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $this->handle($event, ['POST_READ']);
    }

    // Transforms serialized to a Response instance
    public function PRE_RESPOND(ViewEvent $event): void
    {
        $this->handle($event, ['PRE_RESPOND']);
    }
    public function POST_RESPOND(ResponseEvent $event): void
    {
        $this->handle($event, ['POST_RESPOND']);
    }

    // Validates data (POST, PUT)
    public function PRE_VALIDATE(ViewEvent $event): void
    {
        $this->handle($event, ['read', 'PRE_VALIDATE']);
    }
    public function POST_VALIDATE(ViewEvent $event): void
    {
        $this->handle($event, ['POST_VALIDATE']);
    }

    // Serializes the PHP entity in string according to the request format
    public function PRE_SERIALIZE(ViewEvent $event): void
    {
        $this->handle($event, ['write', 'PRE_SERIALIZE']);
    }
    public function POST_SERIALIZE(ViewEvent $event): void
    {
        $this->handle($event, ['POST_SERIALIZE']);
    }

    // Deserializes data into a PHP entity (GET, POST, DELETE);
    // updates the entity retrieved using the data provider (PUT)
    public function PRE_DESERIALIZE(RequestEvent $event): void
    {
        $this->handle($event, ['PRE_DESERIALIZE']);
    }
    public function POST_DESERIALIZE(RequestEvent $event): void
    {
        $this->handle($event, ['POST_DESERIALIZE']);
    }

    // Persists changes in the persistence system
    // using the data persisters (POST, PUT, DELETE)
    public function PRE_WRITE(ViewEvent $event): void
    {
        $this->handle($event, ['PRE_WRITE']);
    }
    public function POST_WRITE(ViewEvent $event): void
    {
        $this->handle($event, ['POST_WRITE']);
    }

}
