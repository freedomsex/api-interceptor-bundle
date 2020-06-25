<?php


namespace FreedomSex\ApiInterceptorBundle\Services\Handler;


abstract class AbstractHandlerCollector
{
    protected $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    public function addHandler($handler)
    {
        $this->handlers[] = $handler;
    }

    public function handle($object, $request = null, $event = null)
    {
        // TODO
    }

}
