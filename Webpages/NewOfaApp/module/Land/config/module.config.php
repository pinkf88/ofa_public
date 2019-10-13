<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Land\Controller\Land' => 'Land\Controller\LandController',
    ),
),
    
    'router' => array(
        'routes' => array(
            'land' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/land[/:action][/:id][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ), 
                    'defaults' => array(
                        'controller' => 'Land\Controller\Land',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'land' => __DIR__ . '/../view',
        ),
    ),
);
 
