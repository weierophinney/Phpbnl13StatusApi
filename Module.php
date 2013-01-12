<?php

namespace Phpbnl13StatusApi;

class Module
{
    public function getAutoloaderConfig()
    {
        return array('Zend\Loader\StandardAutoloader' => array(
            'namespaces' => array(
                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
            ),
        ));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap($e)
    {
        $app    = $e->getTarget();
        $events = $app->getEventManager();
        $events->attach('route', array($this, 'onRoute'), -100);
    }

    public function onRoute($e)
    {
        $matches = $e->getRouteMatch();
        if (!$matches) {
            return;
        }
        $controller = $matches->getParam('controller', false);
        if ($controller != 'Phpbnl13StatusApi\StatusResourceController') {
            return;
        }

        $user = $matches->getParam('user', false);
        if (!$user) {
            return;
        }

        $app          = $e->getTarget();
        $events       = $app->getEventManager();
        $sharedEvents = $events->getSharedManager();

        // Reset the route name in the controller, as we have a user route
        $sharedEvents->attach($controller, 'onDispatch', function ($e) {
            $controller = $e->getTarget();
            $controller->setRoute('phpbnl13_status_user_api');
        }, 100);


        // Set the user in the persistence listener
        $services    = $app->getServiceManager();
        $persistence = $services->get('Phpbnl13StatusApi\PersistenceListener');
        if (!$persistence instanceof StatusPersistenceInterface) {
            return;
        }
        $persistence->setUser($user);
    }
}
