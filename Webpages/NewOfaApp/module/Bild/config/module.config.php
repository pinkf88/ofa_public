<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Bild\Controller\Bild' => 'Bild\Controller\BildController',
    ),
),
    
    'router' => array(
        'routes' => array(
            'bild' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/bild[/:action][/:id][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ), 
                    'defaults' => array(
                        'controller' => 'Bild\Controller\Bild',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'bild' => __DIR__ . '/../view',
        ),
    ),
);
 
