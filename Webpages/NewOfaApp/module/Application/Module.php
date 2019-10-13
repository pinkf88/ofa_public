<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

require_once(realpath(__DIR__ . '/../../vendor/firephp/FirePHPCore/FirePHP.php'));

use Zend\Log\Writer\FirePhp;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Application\Module->onBootstrap()');
		    	
		$eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Application\Module->getConfig()');
		    	
    	return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Application\Module->getAutoloaderConfig()');
		    	
    	return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
