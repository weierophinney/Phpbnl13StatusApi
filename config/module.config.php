<?php
return array(
    'phpbnl13_status_api' => array(
        'table'     => 'status',
        'page_size' => 10, // number of status items to return by default
    ),
    'phlyrestfully' => array(
        'renderer' => array(
            'hydrators' => array(
                'Phpbnl13StatusApi\Status' => 'Hydrator\ClassMethods',
            ),
        ),
    ),
    'router' => array('routes' => array(
        'phpbnl13_status_api' => array(
            'type' => 'Literal',
            'options' => array(
                'route'    => '/api/status',
                'defaults' => array(
                    'controller' => 'Phpbnl13StatusApi\StatusResourcePublicController',
                ),
            ),
            'may_terminate' => false,
            'child_routes' => array(
                'public' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'    => '/public',
                    ),
                ),
                'user' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route'    => '/user/:user[/:id]',
                        'defaults' => array(
                            'controller' => 'Phpbnl13StatusApi\StatusResourceUserController',
                        ),
                        'constraints' => array(
                            'user' => '[a-z0-9_-]+',
                            'id'   => '[a-f0-9]{5,40}',
                        ),
                    ),
                ),
                'documentation' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'    => '/documentation',
                        'defaults' => array(
                            'controller' => 'PhlySimplePage\Controller\Page',
                            'template'   => 'phpbnl13_status_api/documentation',
                        ),
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                        'collection' => array(
                            'type'    => 'Literal',
                            'options' => array(
                                'route'    => '/collection',
                                'defaults' => array(
                                    'template'   => 'phpbnl13_status_api/documentation/collection',
                                ),
                            ),
                        ),
                        'status' => array(
                            'type'    => 'Literal',
                            'options' => array(
                                'route'    => '/status',
                                'defaults' => array(
                                    'template'   => 'phpbnl13_status_api/documentation/status',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    )),
    'service_manager' => array(
        'aliases' => array(
            'Phpbnl13StatusApi\DbAdapter' => 'Zend\Db\Adapter\Adapter',
            'Phpbnl13StatusApi\PersistenceListener' => 'Phpbnl13StatusApi\StatusDbPersistence',
        ),
        'invokables' => array(
            'Hydrator\ClassMethods' => 'Zend\Stdlib\Hydrator\ClassMethods',
        ),
        'factories' => array(
            'Phpbnl13StatusApi\DbTable' => 'Phpbnl13StatusApi\Service\DbTableFactory',
            'Phpbnl13StatusApi\StatusDbPersistence' => 'Phpbnl13StatusApi\Service\StatusDbPersistenceFactory',
            'Phpbnl13StatusApi\StatusResource' => 'Phpbnl13StatusApi\Service\StatusResourceFactory',
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'Phpbnl13StatusApi\StatusResourcePublicController' => 'Phpbnl13StatusApi\Service\StatusResourcePublicControllerFactory',
            'Phpbnl13StatusApi\StatusResourceUserController' => 'Phpbnl13StatusApi\Service\StatusResourceUserControllerFactory',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'phpbnl13_status_api/documentation' => __DIR__ . '/../view/phpbnl13_status_api/documentation.phtml',
            'phpbnl13_status_api/documentation/collection' => __DIR__ . '/../view/phpbnl13_status_api/documentation/collection.phtml',
            'phpbnl13_status_api/documentation/status' => __DIR__ . '/../view/phpbnl13_status_api/documentation/status.phtml',
        ),
    ),
);
