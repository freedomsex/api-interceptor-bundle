<?php


namespace FreedomSex\ApiInterceptorBundle\Annotation;


/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Intercept
{
    public $level;
    public $method;
    public $attributes;
}
