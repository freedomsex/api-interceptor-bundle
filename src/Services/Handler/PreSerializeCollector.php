<?php


namespace FreedomSex\ApiInterceptorBundle\Services\Handler;


use FreedomSex\ApiInterceptorBundle\Services\Handler\PreSerializeHandlerInterface;

class PreSerializeCollector extends AbstractHandlerCollector
{
    public function handle($object, $request = null, $event = null)
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof PreSerializeHandlerInterface) {
                $handler->preSerialize($object, $request, $event);
            }
        }
    }

}
