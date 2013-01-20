<?php

namespace Phpbnl13StatusApi;

use PhlyRestfully\RestfulJsonModel;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

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

        $sharedEvents = $events->getSharedManager();
        $sharedEvents->attach(
            'PhlySimplePage\PageController',
            'dispatch',
            array($this, 'onDispatchDocs'),
            -1
        );
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
        $services     = $app->getServiceManager();
        $events       = $app->getEventManager();
        $sharedEvents = $events->getSharedManager();
        $user         = $matches->getParam('user', false);

        // Add a "Link" header pointing to the documentation
        $sharedEvents->attach('PhlyRestfully\ResourceController', 'dispatch', array($this, 'setDocumentationLink'), 10);

        // Attach the ClassMethods hydrator to the RestfulJsonModel, when found
        $events->attach('render', array($this, 'attachHydratorToRestfulJsonModel'), 200);

        // Set a listener on the createLinks helper to ensure individual status links
        // use the User route, and pass in the user to the route.
        $sharedEvents->attach('PhlyRestfully\ResourceController', 'dispatch', function ($e) use ($user) {
            $controller = $e->getTarget();
            $links      = $controller->links();
            $events     = $links->getEventManager();

            $events->attach('createLink', function ($e) use ($user) {
                $route = $e->getParam('route');
                $params = $e->getParam('params');

                if ($route == 'phpbnl13_status_api/user') {
                    if ($user) {
                        $params['user'] = $user;
                    }
                    return;
                }

                if ($route != 'phpbnl13_status_api/public') {
                    return;
                }

                $item   = $e->getParam('item', false);

                if ($item instanceof Status) {
                    $e->setParam('route', 'phpbnl13_status_api/user');
                    $params['user']  = $item->getUser();
                    return;
                }

                if (!is_array($item)) {
                    return;
                }

                if (!isset($item['user'])) {
                    return;
                }

                $e->setParam('route', 'phpbnl13_status_api/user');
                $params['user']  = $item['user'];
            });
        }, 100);


        if (!$user) {
            return;
        }

        // Set the user in the persistence listener
        $persistence = $services->get('Phpbnl13StatusApi\PersistenceListener');
        if (!$persistence instanceof StatusPersistenceInterface) {
            return;
        }
        $persistence->setUser($user);
    }

    public function onDispatchDocs($e)
    {
        $route = $e->getRouteMatch()->getMatchedRouteName();
        if ($route != 'phpbnl13_status_api/documentation') {
            return;
        }

        $model = $e->getResult();
        $model->setTerminal(true);

        $response = $e->getResponse();
        $response->getHeaders()->addHeaderLine('content-type', 'text/x-markdown');
    }

    public function setDocumentationLink($e)
    {
        $controller = $e->getTarget();
        $docsUrl    = $controller->links()->createLink('phpbnl13_status_api/documentation', false);
        $response   = $e->getResponse();
        $response->getHeaders()->addHeaderLine(
            'Link',
            sprintf('<%s>; rel="describedby"', $docsUrl)
        );
    }

    public function attachHydratorToRestfulJsonModel($e)
    {
        $result = $e->getResult();
        if (!$result instanceof RestfulJsonModel) {
            return;
        }

        $result->addHydrator('Phpbnl13StatusApi\Status', new ClassMethodsHydrator());
    }
}
