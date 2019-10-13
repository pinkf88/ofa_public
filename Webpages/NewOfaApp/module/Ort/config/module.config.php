<?php
$firephp = \FirePHP::getInstance(true);
$firephp->log('module/Ort/config/module.config.php');

return array(
    'controllers' => array(
        'invokables' => array(
            'Ort\Controller\Ort' => 'Ort\Controller\OrtController',
	    ),
	),
    
    'router' => array(
        'routes' => array(
            'ort' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/ort[/:action][/:id][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ), 
                    'defaults' => array(
                        'controller' => 'Ort\Controller\Ort',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'ort' => __DIR__ . '/../view',
        ),
    ),
		
// 	'last_order_by' => 'hallo',
// 	'last_order' => '',
);
 
