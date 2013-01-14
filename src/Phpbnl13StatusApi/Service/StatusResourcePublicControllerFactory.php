<?php

namespace Phpbnl13StatusApi\Service;

use PhlyRestfully\ResourceController;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StatusResourcePublicControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $controllers)
    {
        $services   = $controllers->getServiceLocator();
        $resource   = $services->get('Phpbnl13StatusApi\StatusResource');
        $controller = new ResourceController();
        $controller->setResource($resource);
        $controller->setRoute('phpbnl13_status_api');
        $controller->setHttpOptions(array('GET'));
        return $controller;
    }
}
