<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Serie\Controller\Serie' => 'Serie\Controller\SerieController',
    ),
),
    
    'router' => array(
        'routes' => array(
            'serie' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/serie[/:action][/:id][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ), 
                    'defaults' => array(
                        'controller' => 'Serie\Controller\Serie',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'serie' => __DIR__ . '/../view',
        ),
    ),
);
 
