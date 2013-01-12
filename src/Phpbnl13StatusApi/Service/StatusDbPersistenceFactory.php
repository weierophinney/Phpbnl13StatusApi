<?php

namespace Phpbnl13StatusApi\Service;

use PhlyRestfully\Resource;
use Phpbnl13StatusApi\StatusDbPersistence;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StatusDbPersistenceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $table = $services->get('Phpbnl13StatusApi\DbTable');
        return new StatusDbPersistence($table);
    }
}
