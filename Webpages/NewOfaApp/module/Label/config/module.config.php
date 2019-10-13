<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Label\Controller\Label' => 'Label\Controller\LabelController',
    ),
),
    
    'router' => array(
        'routes' => array(
            'label' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/label[/:action][/:id][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ), 
                    'defaults' => array(
                        'controller' => 'Label\Controller\Label',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'label' => __DIR__ . '/../view',
        ),
    ),
);
 
