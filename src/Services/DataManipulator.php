<?php


namespace FreedomSex\ApiInterceptorBundle\Services;


use Symfony\Component\HttpFoundation\Request;

class DataManipulator
{
    public function __construct(Request $request)
    {
        $this->request = $request;
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
}