<?php


namespace FreedomSex\ApiInterceptorBundle\Services;


use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class RequestResourceExtractor
{
    private $attributes;

    public function extract($request)
    {
        $this->attributes = RequestAttributesExtractor::extractAttributes($request);
        return $this;
    }

    public function extractor()
    {
        return RequestAttributesExtractor::class;
    }

    public function resourceClassName()
    {
        return $this->attributes['resource_class'] ?? null;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function valid()
    {
        return ($this->attributes and $this->resourceClassName());
    }

}
