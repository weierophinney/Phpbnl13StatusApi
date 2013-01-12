<?php

namespace Phpbnl13StatusApi\Service;

use PhlyRestfully\Resource;
use Phpbnl13StatusApi\StatusDbPersistence;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StatusResourceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $events   = $services->get('EventManager');
        $resource = new Resource;
        $resource->setEventManager($events);

        $listener = $services->get('Phpbnl13StatusApi\PersistenceListener');
        $events->attach($listener);

        return $resource;
    }
}
