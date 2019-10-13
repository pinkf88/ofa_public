<?php
namespace Ort;

require_once(realpath(__DIR__ . '/../../vendor/firephp/FirePHPCore/FirePHP.php'));

use Zend\Log\Writer\FirePhp;
use Ort\Model\Ort;
use Ort\Model\OrtTable;
use Ort\Model\Land;
use Ort\Model\LandTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Ort\Module->getAutoloaderConfig()');
    	 
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Ort\Module->getConfig()');
    	    	
        return include __DIR__ . '/config/module.config.php';
    }
 
    public function getServiceConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Ort\Module->getServiceConfig()');
    	
        return array(
            'factories' => array(
                'Ort\Model\OrtTable' =>  function($sm)
                {
                    $dbAdapter1 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table1 = new OrtTable($dbAdapter1);
                    return $table1;
                },
                'Ort\Model\LandTable' =>  function($sm)
                {
                    $dbAdapter2 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table2 = new LandTable($dbAdapter2);
                    return $table2;
                },
            ),
        );
    }
}
 
