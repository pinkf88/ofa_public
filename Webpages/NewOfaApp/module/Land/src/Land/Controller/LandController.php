<?php

namespace Land\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Validator\Db\NoRecordExists;
use Land\Model\Land;
use Land\Model\Kontinent;
use Land\Form\LandForm;
use Land\Form\LandSelectForm;
use Zend\Session\Container;

class LandController extends AbstractActionController
{
    protected $landTable;
    protected $kontinentTable;
    protected $session;

    public function __construct()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LandController->__construct().');
        
        $this->session = new Container('ofa_land');
    }

    public function indexAction()
    {
        $firephp = \FirePHP::getInstance(true);
        
        $kontinentid = 0;
        $countperpage = 250;
        
        if ($this->session->offsetExists('kontinentid'))
        {
            $kontinentid = intval($this->session->offsetGet('kontinentid'));
        }
        
        if ($this->session->offsetExists('countperpage'))
        {
            $countperpage = intval($this->session->offsetGet('countperpage'));
        }
        
        if ($this->getRequest())
        {
            $firephp->log('LandController->indexAction(). getRequest(): isPost=' . $this->getRequest()
                ->isPost());
            
            if ($this->getRequest()
                ->isPost())
            {
                if ($this->getRequest()
                    ->getPost('kontinentid'))
                {
                    $firephp->log('LandController->indexAction(). kontinentid=' . $this->getRequest()
                        ->getPost('kontinentid'));
                    $kontinentid = intval($this->getRequest()
                        ->getPost('kontinentid'));
                }
                else
                {
                    $kontinentid = 0;
                }
                
                if ($this->getRequest()
                    ->getPost('countperpage'))
                {
                    $firephp->log('LandController->indexAction(). countperpage=' . $this->getRequest()
                        ->getPost('countperpage'));
                    $countperpage = intval($this->getRequest()
                        ->getPost('countperpage'));
                }
                else
                {
                    $countperpage = 250;
                }
            }
        }
        
        $this->session->offsetSet('kontinentid', $kontinentid);
        $this->session->offsetSet('countperpage', $countperpage);
        
        $select = new Select();
        
        $select->order('land ' . Select::ORDER_ASCENDING);
        
        if ($kontinentid > 0)
        {
            $select->where('kontinentid=' . $kontinentid);
        }
        
        $paginator = $this->getLandTable()
            ->fetchAll($select);
        $paginator->setCurrentPageNumber((int) $this->params()
            ->fromQuery('page', 1));
        $paginator->setItemCountPerPage($countperpage);
        
        $selectform = new LandSelectForm($this->getKontinentTable()
            ->fetchAll());
        
        if ($kontinentid > 0)
        {
            $selectform->get('kontinentid')
                ->setValue($kontinentid);
        }
        
        $selectform->get('countperpage')
            ->setValue($countperpage);
        
        return new ViewModel(array(
                'paginator' => $paginator,
                'selectform' => $selectform
        ));
    }

    public function addAction()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LandController->addAction()');
        // $firephp->trace('Trace Label');
        
        $dbAdapter = $this->getServiceLocator()
            ->get('Zend\Db\Adapter\Adapter');
        
        $form = new LandForm(null, $dbAdapter);
        $form->get('submit')
            ->setValue('Ok');
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $land = new Land($dbAdapter);
            $form->setInputFilter($land->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid())
            {
                $land->exchangeArray($form->getData());
                
                $validator = new NoRecordExists(array(
                        'adapter' => $dbAdapter,
                        'table' => 'ofa_land',
                        'field' => 'land'
                ));
                
                if ($validator->isValid($land->land))
                {
                    $firephp->log('LandController->addAction(): ' . $land->land . ' ist gültig');
                    
                    $this->getLandTable()
                        ->saveLand($land);
                    
                    // Redirect to list of lands
                    return $this->redirect()
                        ->toRoute('land');
                }
                else
                {
                    $firephp->log('LandController->addAction(): ' . $land->land . ' ist nicht gültig');
                }
            }
        }
        
        return array(
                'form' => $form
        );
    }

    public function editAction()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LandController->editAction()');
        
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('land', array(
                    'action' => 'add'
            ));
        }
        
        $land = '';
        
        // Get the Land with the specified id. An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try
        {
            $land = $this->getLandTable()
                ->getLand($id);
        }
        catch (\Exception $ex)
        {
            $firephp->error('LandController->editAction()');
            
            return $this->redirect()
                ->toRoute('land', array(
                    'action' => 'index'
            ));
        }
        
        $firephp->log('LandController->editAction(). $land=' . $land->land . '. $kontinentid=' . $land->kontinentid);
        $dbAdapter = $this->getServiceLocator()
            ->get('Zend\Db\Adapter\Adapter');
        
        $form = new LandForm(null, $dbAdapter);
        $form->bind($land);
        
        $form->get('submit')
            ->setValue('Ändern');
        $form->get('kontinentid')
            ->setValue($land->kontinentid);
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $firephp->log('isPost -> LandController->editAction()');
            
            $form->setInputFilter($land->getInputFilter());
            $form->setData($request->getPost());
            
            // print_r($request->getPost());
            
            if ($form->isValid())
            {
                $firephp->log('isValid=true -> LandController->editAction()');
                
                $this->getLandTable()
                    ->saveLand($land);
                
                // Redirect to list of lands
                return $this->redirect()
                    ->toRoute('land');
            }
            
            $firephp->warn('isValid=false -> LandController->editAction()');
        }
        
        return array(
                'id' => $id,
                'form' => $form
        );
    }

    public function deleteAction()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LandController->deleteAction()');
        
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('land');
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $del = $request->getPost('del', 'Nein');
            
            if ($del == 'Ja')
            {
                $id = (int) $request->getPost('id');
                $this->getLandTable()
                    ->deleteLand($id);
            }
            
            // Redirect to list of lands
            return $this->redirect()
                ->toRoute('land');
        }
        
        return array(
                'id' => $id,
                'land' => $this->getLandTable()
                    ->getLand($id)
        );
    }

    public function getLandTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LandController->getLandTable()');
        
        if (! $this->landTable)
        {
            $sm = $this->getServiceLocator();
            $this->landTable = $sm->get('Land\Model\LandTable');
        }
        
        return $this->landTable;
    }

    public function getKontinentTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LandController->getKontinentTable()');
        
        if (! $this->kontinentTable)
        {
            $sm = $this->getServiceLocator();
            $this->kontinentTable = $sm->get('Land\Model\KontinentTable');
        }
        
        return $this->kontinentTable;
    }
}
