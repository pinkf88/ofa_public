<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Ticket\Controller\Ticket' => 'Ticket\Controller\TicketController',
    ),
),
    
    'router' => array(
        'routes' => array(
            'ticket' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/ticket[/:action][/:id][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'action' => '(?!\border_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ), 
                    'defaults' => array(
                        'controller' => 'Ticket\Controller\Ticket',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'ticket' => __DIR__ . '/../view',
        ),
    ),
);
 
