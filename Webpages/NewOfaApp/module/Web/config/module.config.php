<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Web\Controller\Web' => 'Web\Controller\WebController',
    ),
),
    
    'router' => array(
        'routes' => array(
            'web' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/web[/:action][/:id][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ), 
                    'defaults' => array(
                        'controller' => 'Web\Controller\Web',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'web' => __DIR__ . '/../view',
        ),
    ),
);
 
