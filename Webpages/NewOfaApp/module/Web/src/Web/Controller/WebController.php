<?php

namespace Web\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Web\Model\Web;
use Web\Form\WebForm;
use Web\Form\WebSelectForm;

class WebController extends AbstractActionController
{
    protected $webTable;
    protected $webSerieTable;
    protected $landTable;
    protected $kontinentTable;
    protected $session;

    public function __construct()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebController->__construct().');
        
        $this->session = new Container('ofa_web');
    }

    public function indexAction()
    {
        $firephp = \FirePHP::getInstance(true);

        $countperpage = 200;
        
        if ($this->session->offsetExists('countperpage'))
        {
            $countperpage = intval($this->session->offsetGet('countperpage'));
        }
        
        if ($this->getRequest())
        {
            $firephp->log('WebController->indexAction(). getRequest(): isPost=' . $this->getRequest()->isPost());
            
            if ($this->getRequest()->isPost())
            {
                if ($this->getRequest()->getPost('countperpage'))
                {
                    $firephp->log('WebController->indexAction(). countperpage=' . $this->getRequest()->getPost('countperpage'));
                    $countperpage = intval($this->getRequest()->getPost('countperpage'));
                }
                else
                {
                    $countperpage = 200;
                }
            }
        }
        
        $this->session->offsetSet('countperpage', $countperpage);
        
        $select = new Select();
        $select->order('web ' . Select::ORDER_ASCENDING);
        
        $firephp->log('WebController->indexAction().');
        
        $paginator = $this->getWebTable()->fetchAll($select);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage($countperpage);

        $this->session->offsetSet('page', $this->params()->fromQuery('page', 1));
        
        $selectform = new WebSelectForm();
        
        $selectform->get('countperpage')->setValue($countperpage);
        
        return new ViewModel(array(
                'paginator' => $paginator,
                'selectform' => $selectform
        ));
    }

    public function addAction()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebController->addAction()');
        // $firephp->trace('Trace Label');
        
        $session = $this->session;
        
        $form = new WebForm($this->getLandTable()->fetchAll());
        $form->get('submit')->setValue('Ok');
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $firephp->log('WebController->addAction(): isPost');
            
            $web = new Web();
            $form->setInputFilter($web->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid())
            {
                $firephp->log('WebController->addAction(): isValid');
                
                $web->exchangeArray($form->getData());
                
                if ($web->landid > 0)
                {
                    $web->web = $this->getLandTable()->getLand($web->landid)->land;
                    $web->pfad = '/' . strtolower ($this->getKontinentTable()->getKontinent($this->getLandTable()->getLand($web->landid)->kontinentid)->kontinent)
                               . '/' . strtolower ($this->getLandTable()->getLand($web->landid)->kurz)
                               . '/';
                }
                
                $this->getWebTable()->saveWeb($web);
                
                if ($request->getPost('submit'))
                    return $this->redirect()
                        ->toRoute('web');
            }
            
            $firephp->warn('WebController->addAction(): isValid=false');
        }
        
        return array(
                'form' => $form
        );
    }

    public function editAction()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebController->editAction()');
        
        $session = $this->session;
        
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('web', array(
                    'action' => 'add'
            ));
        }
        
        $web = '';
        
        // Get the Web with the specified id. An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try
        {
            $web = $this->getWebTable()
                ->getWeb($id);
        }
        catch (\Exception $ex)
        {
            $firephp->error('WebController->editAction()');
            
            return $this->redirect()
                ->toRoute('web', array(
                    'action' => 'index'
            ));
        }
        
//         $firephp->log('WebController->editAction(). $nr=' . $web->nr . '. $ortid=' . $web->ortid . '. $beschreibung=' . $web->beschreibung);
        
        $form = new WebForm($this->getLandTable()->fetchAll());
        $form->bind($web);
        $form->get('submit')->setValue('Ändern');
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $firephp->log('isPost -> WebController->editAction()');
            $firephp->log('WebController->editAction(). web=' . $web->web . '. landid=' . $web->landid . '. pfad=' . $web->pfad . '.');
            
            $form->setInputFilter($web->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid())
            {
                $firephp->log('isValid -> WebController->editAction()');
                                
                $this->getWebTable()->saveWeb($web);

                $page = '1';
                
                // Redirect to list of webs
                if ($this->session->offsetExists('page'))
                {
                    $page = $this->session->offsetGet('page') . '#' . $id;
                }
                
                return $this->redirect()->toUrl('/web?page=' . $page);
            }
            
            $firephp->warn('isValid=false -> WebController->editAction()');
        }
        
        return array(
                'id' => $id,
                'form' => $form
        );
    }

    public function deleteAction()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebController->deleteAction()');
        
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('web');
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $del = $request->getPost('del', 'Nein');
            
            if ($del == 'Ja')
            {
                $id = (int) $request->getPost('id');
                
                $this->getWebTable()
                    ->deleteWeb($id);
                
                $this->getWebSerieTable()
                    ->deleteWebSerie($id);
            }
            
            // Redirect to list of webs
            return $this->redirect()
                ->toRoute('web');
        }
        
        return array(
                'id' => $id,
                'web' => $this->getWebTable()
                    ->getWeb($id)
        );
    }

    public function getWebTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebController->getWebTable()');
        
        if (! $this->webTable)
        {
            $sm = $this->getServiceLocator();
            $this->webTable = $sm->get('Web\Model\WebTable');
        }
        
        return $this->webTable;
    }

    public function getWebSerieTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebController->getWebSerieTable()');
        
        if (! $this->webSerieTable)
        {
            $sm = $this->getServiceLocator();
            $this->webSerieTable = $sm->get('Web\Model\WebSerieTable');
        }
        
        return $this->webSerieTable;
    }

    public function getLandTable()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('WebController->getLandTable()');
    	
    	if (!$this->landTable)
        {
            $sm = $this->getServiceLocator();
            $this->landTable = $sm->get('Web\Model\LandTable');
        }

        return $this->landTable;
    }

    public function getKontinentTable()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('WebController->getKontinentTable()');
    	
    	if (!$this->kontinentTable)
        {
            $sm = $this->getServiceLocator();
            $this->kontinentTable = $sm->get('Web\Model\KontinentTable');
        }

        return $this->kontinentTable;
    }
}
