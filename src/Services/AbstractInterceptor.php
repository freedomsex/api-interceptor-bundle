<?php


namespace FreedomSex\ApiInterceptorBundle\Services;


use FreedomSex\ApiInterceptorBundle\Contract\InterceptorInterface;

abstract class AbstractInterceptor implements InterceptorInterface
{
    protected $resourceClassName;
    protected $dataManipulator;
    protected $event;

    public function setClassName($resourceClassName)
    {
        $this->resourceClassName = $resourceClassName;
    }

    public function setEvent($event)
    {
        return $this->event = $event;
    }

    public function className()
    {
        return $this->resourceClassName;
    }

    public function setDataManipulator($dataManipulator)
    {
        $this->dataManipulator = $dataManipulator;
        return $dataManipulator;
    }

    public function params($key)
    {
        return $this->dataManipulator->params($key);
    }

    public function data($class = null)
    {
        $object = $this->dataManipulator->getData($class);
        if (!$object) {
            throw new BadRequestHttpException();
        }
        return $object;
    }

    public function results($event = null)
    {
        $event = $event ?: $this->event;
        $object = $this->dataManipulator->getResults($event);
        if (!$object) {
            return [];
        }
        return $object;
    }

    public function first($event = null)
    {
        $event = $event ?: $this->event;
        $object = $this->results($event);
        if (!$object) {
            return null;
        }
        $array = $object->getIterator();
        if (!$array or !count($array)) {
            return null;
        }
        return $array[0];
    }


}