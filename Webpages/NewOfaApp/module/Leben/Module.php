<?php

namespace Leben;

require_once (realpath(__DIR__ . '/../../vendor/firephp/FirePHPCore/FirePHP.php'));

use Zend\Log\Writer\FirePhp;
use Leben\Model\Leben;
use Leben\Model\LebenTable;
use Leben\Model\Kontinent;
use Leben\Model\OrtTable;
use Leben\Model\LandTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{

    public function getAutoloaderConfig()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Leben\Module->getAutoloaderConfig()');
        
        return array(
                'Zend\Loader\ClassMapAutoloader' => array(
                        __DIR__ . '/autoload_classmap.php'
                ),
                'Zend\Loader\StandardAutoloader' => array(
                        'namespaces' => array(
                                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                        )
                )
        );
    }

    public function getConfig()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Leben\Module->getConfig()');
        
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Leben\Module->getServiceConfig()');
        
        return array(
                'factories' => array(
                        'Leben\Model\LebenTable' => function ($sm)
                        {
                            $dbAdapter1 = $sm->get('Zend\Db\Adapter\Adapter');
                            $table1 = new LebenTable($dbAdapter1);
                            return $table1;
                        },
                        'Leben\Model\OrtTable' => function ($sm)
                        {
                            $dbAdapter2 = $sm->get('Zend\Db\Adapter\Adapter');
                            $table2 = new OrtTable($dbAdapter2);
                            return $table2;
                        },
                        'Leben\Model\LandTable' => function ($sm)
                        {
                            $dbAdapter3 = $sm->get('Zend\Db\Adapter\Adapter');
                            $table3 = new LandTable($dbAdapter3);
                            return $table3;
                        }
                )
        );
    }
}
 
