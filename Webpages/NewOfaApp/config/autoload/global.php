<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

// require_once(realpath(__DIR__ . '/../../vendor/firephp/FirePHPCore/FirePHP.php'));

// use Zend\Log\Writer\FirePhp;

// $firephp = \FirePHP::getInstance(true);
// $firephp->log('global.php');


return array(
    'db' => array(
        'driver'         => 'Pdo',
        // 'dsn'            => 'mysql:dbname=myofa;host=192.168.2.227',
        'dsn'            => 'mysql:dbname=myofa;host=DISKSTATION',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'service_manager' => array(
    'factories' => array(
        'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
	    ),
    ),
);
