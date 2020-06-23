<?php


namespace FreedomSex\ApiInterceptorBundle\Services\Handler;


interface PreSerializeHandlerInterface
{
    public function preSerialize($object, $request, $event);
}
