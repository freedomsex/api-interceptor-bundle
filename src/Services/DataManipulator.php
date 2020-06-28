<?php


namespace FreedomSex\ApiInterceptorBundle\Services;


use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class DataManipulator
{
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getResults(ViewEvent $event)
    {
        $results = $event->getControllerResult();
        if (!$results or $results instanceof Response) {
            return;
        }
        if (!is_iterable($results)) {
            $results = [$results];
        }
        return $results;
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