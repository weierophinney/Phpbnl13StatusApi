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
        if (!in_array(
            $controller, 
            array('Phpbnl13StatusApi\StatusResourcePublicController', 'Phpbnl13StatusApi\StatusResourceUserController')
        )) {
            return;
        }

        $app          = $e->getTarget();
        $events       = $app->getEventManager();
        $sharedEvents = $events->getSharedManager();

        // Set a listener on the createLinks helper to ensure individual status links
        // use the User route, and pass in the user to the route.
        $sharedEvents->attach('Phpbnl13StatusApi\StatusResourceController', 'dispatch', function ($e) {
            $controller = $e->getTarget();
            $links      = $controller->links();
            $events     = $links->getEventManager();

            $events->attach('createLinks', function ($e) {
                $route = $e->getParam('route');
                if ($route != 'phpbnl13_status_api') {
                    return;
                }

                $params = $e->getParam('params');
                $item   = $e->getParam('item', false);

                if ($item instanceof Status) {
                    $params['route'] = 'phpbnl13_status_user_api';
                    $params['user']  = $item->getUser();
                    return;
                }

                if (!is_array($item)) {
                    return;
                }

                if (!isset($item['user'])) {
                    return;
                }

                $e->setParam('route', 'phpbnl13_status_user_api');
                $params['user']  = $item['user'];
            });
        }, 100);


        $user = $matches->getParam('user', false);
        if (!$user) {
            return;
        }

        // Set the user in the persistence listener
        $services    = $app->getServiceManager();
        $persistence = $services->get('Phpbnl13StatusApi\PersistenceListener');
        if (!$persistence instanceof StatusPersistenceInterface) {
            return;
        }
        $persistence->setUser($user);
    }
}
