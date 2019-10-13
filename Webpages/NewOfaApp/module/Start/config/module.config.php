<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Start\Controller\Start' => 'Start\Controller\StartController',
    ),
),
    
    'router' => array(
        'routes' => array(
            'start' => array(
                'type'    => 'segment',
                'options' => array(
                    'defaults' => array(
                        'controller' => 'Start\Controller\Start',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'start' => __DIR__ . '/../view',
        ),
    ),
);
 
