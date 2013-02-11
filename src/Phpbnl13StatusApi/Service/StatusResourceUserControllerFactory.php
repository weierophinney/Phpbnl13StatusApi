<?php

namespace Phpbnl13StatusApi\Service;

use PhlyRestfully\ResourceController;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StatusResourceUserControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $controllers)
    {
        $services   = $controllers->getServiceLocator();
        $resource   = $services->get('Phpbnl13StatusApi\StatusResource');
        $config     = $services->get('config');
        $config     = isset($config['phpbnl13_status_api']) ? $config['phpbnl13_status_api'] : array();
        $pageSize   = isset($config['page_size']) ? $config['page_size'] : 10;

        $controller = new ResourceController('Phpbnl13StatusApi\StatusResourceController');
        $controller->setResource($resource);
        $controller->setPageSize($pageSize);
        $controller->setRoute('phpbnl13_status_api/user');
        $controller->setCollectionName('status');
        return $controller;
    }
}
