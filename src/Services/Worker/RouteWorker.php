<?php


namespace FreedomSex\ApiInterceptorBundle\Services\Worker;


use FreedomSex\ApiInterceptorBundle\Services\ResourceRoutesTrait;

class RouteWorker extends AbstractWorker
{
    use ResourceRoutesTrait;

    const ITEM_METHODS = ['GET', 'PUT', 'PATCH', 'DELETE'];
    const COLLECTION_METHODS = ['GET', 'POST'];

    public function handleRoute($attributes)
    {
        if ($this->isItem()) {
            if (!in_array($this->method(), self::ITEM_METHODS)) {
               return;
            }
            $prefix = 'item';
        } else {
            if (!in_array($this->method(), self::COLLECTION_METHODS)) {
                return;
            }
            $prefix = 'collection';
        }
        $action = $prefix.$this->method();
        $this->{$action}($attributes);
    }

    public function run($request, $attributes = null, $event = null)
    {
        $this->event = $event;
        $this->request = $request;
        $this->handleRoute($attributes);
    }
}
