<?php
return array(
    'phpbnl13_status_api' => array(
        'table' => 'status',
    ),
    'router' => array('routes' => array(
        'phpbnl13_status_api' => array(
            'type' => 'Segment',
            'options' => array(
                'route'    => '/api/status[/:id]]',
                'defaults' => array(
                    'controller' => 'Phpbnl13StatusApi\StatusResourceController',
                ),
                'constraints' => array(
                    'id' => '^[a-f0-9]{5,40}$',
                ),
            ),
        ),
        'phpbnl13_status_user_api' => array(
            'type' => 'Segment',
            'options' => array(
                'route'    => '/api/status/:user[/:id]]',
                'defaults' => array(
                    'controller' => 'Phpbnl13StatusApi\StatusResourceController',
                ),
                'constraints' => array(
                    'user' => '^[a-z0-9_-]+$',
                    'id'   => '^[a-f0-9]{5,40}$',
                ),
            ),
        ),
    )),
    'service_manager' => array(
        'aliases' => array(
            'Phpbnl13StatusApi\DbAdapter' => 'Zend\Db\Adapter\Adapter',
            'Phpbnl13StatusApi\PersistenceListener' => 'Phpbnl13StatusApi\StatusDbPersistence',
        ),
        'factories' => array(
            'Phpbnl13StatusApi\DbTable' => 'Phpbnl13StatusApi\Service\DbTableFactory',
            'Phpbnl13StatusApi\StatusDbPersistence' => 'Phpbnl13StatusApi\Service\StatusDbPersistenceFactory',
            'Phpbnl13StatusApi\StatusResource' => 'Phpbnl13StatusApi\Service\StatusResourceFactory',
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'Phpbnl13StatusApi\StatusResourceController' => 'Phpbnl13StatusApi\Service\StatusResourceControllerFactory',
        ),
    ),
);
