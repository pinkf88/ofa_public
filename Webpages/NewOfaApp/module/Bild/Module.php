<?php
namespace Bild;

// require_once(realpath(__DIR__ . '/../../vendor/firephp/FirePHPCore/FirePHP.php'));

// use Zend\Log\Writer\FirePhp;
use Bild\Model\Bild;
use Bild\Model\BildTable;
use Bild\Model\Ort;
use Bild\Model\OrtTable;
use Bild\Model\Land;
use Bild\Model\LandTable;
use Bild\Model\Motiv;
use Bild\Model\MotivTable;
use Bild\Model\BildMotiv;
use Bild\Model\BildMotivTable;
use Bild\Model\Serie;
use Bild\Model\SerieTable;
use Bild\Model\Info;
use Bild\Model\InfoTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
    	// $firephp = \FirePHP::getInstance(true);
    	// $firephp->log('Bild\Module->getAutoloaderConfig()');
    	 
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
    	// $firephp = \FirePHP::getInstance(true);
    	// $firephp->log('Bild\Module->getConfig()');
    	    	
        return include __DIR__ . '/config/module.config.php';
    }
 
    public function getServiceConfig()
    {
    	// $firephp = \FirePHP::getInstance(true);
    	// $firephp->log('Bild\Module->getServiceConfig()');
    	
        return array(
            'factories' => array(
                'Bild\Model\BildTable' =>  function($sm)
                {
                    $dbAdapter1 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table1 = new BildTable($dbAdapter1);
                    return $table1;
                },
                'Bild\Model\OrtTable' =>  function($sm)
                {
                    $dbAdapter2 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table2 = new OrtTable($dbAdapter2);
                    return $table2;
                },
                'Bild\Model\LandTable' =>  function($sm)
                {
                    $dbAdapter3 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table3 = new LandTable($dbAdapter3);
                    return $table3;
                },
                'Bild\Model\MotivTable' =>  function($sm)
                {
                    $dbAdapter4 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table4 = new MotivTable($dbAdapter4);
                    return $table4;
                },
                'Bild\Model\BildMotivTable' =>  function($sm)
                {
                    $dbAdapter5 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table5 = new BildMotivTable($dbAdapter5);
                    return $table5;
                },
                'Bild\Model\SerieTable' =>  function($sm)
                {
                    $dbAdapter6 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table6 = new SerieTable($dbAdapter6);
                    return $table6;
                },
                'Bild\Model\InfoTable' =>  function($sm)
                {
                    $dbAdapter7 = $sm->get('Zend\Db\Adapter\Adapter');
                    $table7 = new InfoTable($dbAdapter7);
                    return $table7;
                },
            ),
        );
    }
}
