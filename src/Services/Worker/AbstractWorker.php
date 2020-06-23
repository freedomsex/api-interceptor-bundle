<?php


namespace FreedomSex\ApiInterceptorBundle\Services\Worker;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

abstract class AbstractWorker implements WorkerInterface
{
    /* @var ControllerEvent */
    protected $event;
    /* @var Request */
    protected $request;

    public function isItem()
    {
        return $this->request->attributes->get('_api_item_operation_name');
    }

    public function method()
    {
        return strtoupper($this->request->getMethod());
    }

    public function response()
    {
        return $this->event->getResponse();
    }

    public function getData($className)
    {
        $data = $this->request->attributes->get('data');
        if ($className and !($data instanceof $className)) {
            return;
        }
        return $data;
    }

    public function setData($data)
    {
        $this->request->attributes->set('data', $data);
    }

    public function run($request)
    {
        // TODO: Implement run() method.
    }
}
