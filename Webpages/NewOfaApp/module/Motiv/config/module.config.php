<?php
$firephp = \FirePHP::getInstance(true);
$firephp->log('module/Motiv/config/module.config.php');

return array(
    'controllers' => array(
        'invokables' => array(
            'Motiv\Controller\Motiv' => 'Motiv\Controller\MotivController',
	    ),
	),
    
    'router' => array(
        'routes' => array(
            'motiv' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/motiv[/:action][/:id][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ), 
                    'defaults' => array(
                        'controller' => 'Motiv\Controller\Motiv',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'motiv' => __DIR__ . '/../view',
        ),
    ),
		
// 	'last_order_by' => 'hallo',
// 	'last_order' => '',
);
 
