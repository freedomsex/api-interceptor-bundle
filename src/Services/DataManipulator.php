<?php


namespace FreedomSex\ApiInterceptorBundle\Services;


use Symfony\Component\HttpFoundation\RequestStack;

class DataManipulator
{
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
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