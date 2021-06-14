<?php


namespace FreedomSex\ApiInterceptorBundle\Services;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class InterceptorHandler
{
    const ITEM_METHODS = ['GET', 'PUT', 'PATCH', 'DELETE'];
    const COLLECTION_METHODS = ['GET', 'POST'];

    protected $event;
    protected $resourceClassName;
    /* @var Request */
    protected $request;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function isItem()
    {
        return $this->request->attributes->get('_api_item_operation_name');
    }

    public function method()
    {
        return strtoupper($this->request->getMethod());
    }

    public function setup($event, $resourceClassName)
    {
        $this->event = $event;
        $this->request = $event->getRequest();
        $this->resourceClassName = $resourceClassName;
    }

    public function limitation($interceptor)
    {
        if (!$this->isItem() and $interceptor->item === true) {
            return true;
        }
        if ($this->isItem() and $interceptor->item !== true) {
            return true;
        }
        if (!is_null($interceptor->method)) {
            if (strcasecmp($interceptor->method, $this->method())) {
                return true;
            }
        }
        return false;
    }

    public function handle($level, $intercept, $method)
    {
        if ($intercept->level and strcasecmp($level, $intercept->level)) {
            return;
        }
        if ($intercept->method and strcasecmp($this->method(), $intercept->method)) {
            return;
        }

        if ($this->isItem()) {
            if (!in_array($this->method(), self::ITEM_METHODS)) {
                return;
            }
        } else {
            if (!in_array($this->method(), self::COLLECTION_METHODS)) {
                return;
            }
        }
        $interceptor = $this->container->get($method->class);
        if (method_exists($interceptor, 'setEvent')) {
            $interceptor->setEvent($this->event);
        }
        if (method_exists($interceptor, 'setClassName')) {
            $interceptor->setClassName($this->resourceClassName);
        }
        if (is_subclass_of($interceptor, 'AbstractInterceptor')) {
            $data = $interceptor->data($this->resourceClassName);
            $interceptor->{$method->name}($data, $this->event, $intercept->attributes);
        } else {
            $interceptor->{$method->name}($this->event, $intercept->attributes);
        }
    }

}
