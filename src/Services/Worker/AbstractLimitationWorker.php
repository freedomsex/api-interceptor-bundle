<?php


namespace FreedomSex\ApiInterceptorBundle\Services\Worker;

// DRAFT
// DRAFT
// DRAFT
// DRAFT
// DRAFT

use FreedomSex\ApiInterceptorBundle\Services\Worker\AbstractWorker;
use FreedomSex\ApiInterceptorBundle\Services\Worker\WorkerInterface;

class AbstractLimitationWorker extends AbstractWorker implements WorkerInterface
{

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

    public function handle()
    {
        if ($this->limitation($interceptor)) {
            return;
        }
    }

    public function run($request, $attributes = null, $event = null)
    {
        $this->event = $event;
        $this->request = $request;
        $this->handle($request, $attributes, $event);
    }
}
