<?php
namespace Ticket;

require_once(realpath(__DIR__ . '/../../vendor/firephp/FirePHPCore/FirePHP.php'));

use Zend\Log\Writer\FirePhp;
use Ticket\Model\Ticket;
use Ticket\Model\TicketTable;
use Ticket\Model\Kontinent;
use Ticket\Model\KontinentTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Ticket\Module->getAutoloaderConfig()');
    	 
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
    	$firephp->log('Ticket\Module->getConfig()');
    	    	
        return include __DIR__ . '/config/module.config.php';
    }
 
    public function getServiceConfig()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('Ticket\Module->getServiceConfig()');
    	
        return array(
            'factories' => array(
                'Ticket\Model\TicketTable' =>  function($sm)
                {
                    $dbAdapter1 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table1 = new TicketTable($dbAdapter1);
                    return $table1;
                },
                'Ticket\Model\KontinentTable' =>  function($sm)
                {
                    $dbAdapter2 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table2 = new KontinentTable($dbAdapter2);
                    return $table2;
                },
            ),
        );
    }
}
 
