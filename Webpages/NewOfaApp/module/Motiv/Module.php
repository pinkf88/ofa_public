<?php
namespace Motiv;

require_once(realpath(__DIR__ . '/../../vendor/firephp/FirePHPCore/FirePHP.php'));

use Zend\Log\Writer\FirePhp;
use Motiv\Model\Motiv;
use Motiv\Model\MotivTable;
use Motiv\Model\Ort;
use Motiv\Model\OrtTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Motiv\Module->getAutoloaderConfig()');
    	 
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
    	$firephp->log('Motiv\Module->getConfig()');
    	    	
        return include __DIR__ . '/config/module.config.php';
    }
 
    public function getServiceConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Motiv\Module->getServiceConfig()');
    	
        return array(
            'factories' => array(
                'Motiv\Model\MotivTable' =>  function($sm)
                {
                    $dbAdapter1 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table1 = new MotivTable($dbAdapter1);
                    return $table1;
                },
                'Motiv\Model\OrtTable' =>  function($sm)
                {
                    $dbAdapter2 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table2 = new OrtTable($dbAdapter2);
                    return $table2;
                },
            ),
        );
    }
}
 
