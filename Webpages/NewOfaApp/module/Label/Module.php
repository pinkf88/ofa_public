<?php
namespace Label;

require_once(realpath(__DIR__ . '/../../vendor/firephp/FirePHPCore/FirePHP.php'));

use Zend\Log\Writer\FirePhp;
use Label\Model\Label;
use Label\Model\LabelTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Label\Module->getAutoloaderConfig()');
    	 
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
    	$firephp->log('Label\Module->getConfig()');
    	    	
        return include __DIR__ . '/config/module.config.php';
    }
 
    public function getServiceConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Label\Module->getServiceConfig()');
    	
        return array(
            'factories' => array(
                'Label\Model\LabelTable' =>  function($sm)
                {
                    $dbAdapter1 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table1 = new LabelTable($dbAdapter1);
                    return $table1;
                },
            ),
        );
    }
}
 
