<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Leben\Controller\Leben' => 'Leben\Controller\LebenController',
    ),
),
    
    'router' => array(
        'routes' => array(
            'leben' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/leben[/:action][/:id][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ), 
                    'defaults' => array(
                        'controller' => 'Leben\Controller\Leben',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'leben' => __DIR__ . '/../view',
        ),
    ),
);
 
