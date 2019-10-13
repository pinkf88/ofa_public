<?php
namespace Start\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Land\Model\Land;
use Land\Model\Kontinent;
use Land\Form\LandForm;

class StartController extends AbstractActionController
{
    public function indexAction()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('StartController->indexAction()');

        return new ViewModel(array());
    }
}
