<?php
namespace Web;

require_once(realpath(__DIR__ . '/../../vendor/firephp/FirePHPCore/FirePHP.php'));

use Zend\Log\Writer\FirePhp;
use Web\Model\WebTable;
use Web\Model\WebSerieTable;
use Web\Model\LandTable;
use Web\Model\KontinentTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Web\Module->getAutoloaderConfig()');
    	 
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
    	$firephp->log('Web\Module->getConfig()');
    	    	
        return include __DIR__ . '/config/module.config.php';
    }
 
    public function getServiceConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Web\Module->getServiceConfig()');
    	
        return array(
            'factories' => array(
                'Web\Model\WebTable' =>  function($sm)
                {
                    $dbAdapter1 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table1 = new WebTable($dbAdapter1);
                    return $table1;
                },
                'Web\Model\WebSerieTable' =>  function($sm)
                {
                    $dbAdapter2 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table2 = new WebSerieTable($dbAdapter2);
                    return $table2;
                },
                'Web\Model\LandTable' =>  function($sm)
                {
                    $dbAdapter3 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table3 = new LandTable($dbAdapter3);
                    return $table3;
                },
                'Web\Model\KontinentTable' =>  function($sm)
                {
                    $dbAdapter4 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table4 = new KontinentTable($dbAdapter4);
                    return $table4;
                },
            ),
        );
    }
}
