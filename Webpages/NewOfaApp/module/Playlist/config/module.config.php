<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Playlist\Controller\Playlist' => 'Playlist\Controller\PlaylistController',
        ),
    ),
    
    'router' => array(
        'routes' => array(
            'playlist' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/playlist',
                    'constraints' => array(),
                    'defaults' => array(
                        'controller' => 'Playlist\Controller\Playlist',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'playlist' => __DIR__ . '/../view',
        ),
    ),
);
