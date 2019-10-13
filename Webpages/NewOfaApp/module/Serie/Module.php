<?php
namespace Serie;

require_once(realpath(__DIR__ . '/../../vendor/firephp/FirePHPCore/FirePHP.php'));

use Zend\Log\Writer\FirePhp;
use Serie\Model\SerieTable;
use Serie\Model\SerieBildTable;
use Serie\Model\WebTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Serie\Module->getAutoloaderConfig()');
    	 
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
    	$firephp->log('Serie\Module->getConfig()');
    	    	
        return include __DIR__ . '/config/module.config.php';
    }
 
    public function getServiceConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Serie\Module->getServiceConfig()');
    	
        return array(
            'factories' => array(
                'Serie\Model\SerieTable' =>  function($sm)
                {
                    $dbAdapter1 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table1 = new SerieTable($dbAdapter1);
                    return $table1;
                },
                'Serie\Model\SerieBildTable' =>  function($sm)
                {
                    $dbAdapter2 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table2 = new SerieBildTable($dbAdapter2);
                    return $table2;
                },
                'Serie\Model\WebTable' =>  function($sm)
                {
                    $dbAdapter3 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table3 = new WebTable($dbAdapter3);
                    return $table3;
                },
            ),
        );
    }
}
