<?php


namespace FreedomSex\ApiInterceptorBundle\Annotation;


/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Interceptor
{
    public $source;
    public $name;
    public $prefix;
}
