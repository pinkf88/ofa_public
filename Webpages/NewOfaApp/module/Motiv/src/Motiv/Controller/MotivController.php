<?php

namespace Motiv\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Motiv\Model\Motiv;
use Motiv\Model\Ort;
use Motiv\Form\MotivForm;
use Motiv\Form\MotivSelectForm;
use Zend\Session\Container;

class MotivController extends AbstractActionController
{
    protected $motivTable;
    protected $ortTable;
    protected $session;

    public function __construct()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('MotivController->__construct().');
        
        $this->session = new Container('ofa_motiv');
    }

    public function indexAction()
    {
        $firephp = \FirePHP::getInstance(true);
        
        $ortid = 0;
        $countperpage = 250;
        
        if ($this->session->offsetExists('ortid'))
        {
            $ortid = intval($this->session->offsetGet('ortid'));
        }
        
        if ($this->session->offsetExists('countperpage'))
        {
            $countperpage = intval($this->session->offsetGet('countperpage'));
        }
        
        if ($this->getRequest())
        {
            $firephp->log('MotivController->indexAction(). getRequest(): isPost=' . $this->getRequest()
                ->isPost());
            
            if ($this->getRequest()
                ->isPost())
            {
                if ($this->getRequest()
                    ->getPost('ortid'))
                {
                    $firephp->log('MotivController->indexAction(). ortid=' . $this->getRequest()
                        ->getPost('ortid'));
                    $ortid = intval($this->getRequest()
                        ->getPost('ortid'));
                }
                else
                {
                    $ortid = 0;
                }
                
                if ($this->getRequest()
                    ->getPost('countperpage'))
                {
                    $firephp->log('MotivController->indexAction(). countperpage=' . $this->getRequest()
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
        
        $this->session->offsetSet('ortid', $ortid);
        $this->session->offsetSet('countperpage', $countperpage);
        
        $select = new Select();
        
        $select->order('motiv ' . Select::ORDER_ASCENDING);
        
        if ($ortid > 0)
        {
            $select->where('ortid=' . $ortid);
        }
        
        $paginator = $this->getMotivTable()
            ->fetchAll($select);
        $paginator->setCurrentPageNumber((int) $this->params()
            ->fromQuery('page', 1));
        $paginator->setItemCountPerPage($countperpage);
        
        $this->session->offsetSet('page', $this->params()
            ->fromQuery('page', 1));
        
        $selectform = new MotivSelectForm($this->getOrtTable()
            ->fetchAll());
        
        if ($ortid > 0)
        {
            $selectform->get('ortid')
                ->setValue($ortid);
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
        $firephp->log('MotivController->addAction()');
        // $firephp->trace('Trace Label');
        
        $form = new MotivForm($this->getOrtTable()
            ->fetchAll());
        $form->get('submit')
            ->setValue('Hinzufügen');
        
        if ($this->session->offsetExists('input_ortid'))
        {
            $form->get('ortid')
                ->setValue($this->session->offsetGet('input_ortid'));
        }
        else if ($this->session->offsetExists('ortid'))
        {
            $form->get('ortid')
                ->setValue($this->session->offsetGet('ortid'));
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $motiv = new Motiv();
            $form->setInputFilter($motiv->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid())
            {
                $motiv->exchangeArray($form->getData());
                
                $this->session->offsetSet('input_ortid', $motiv->ortid);
                
                $id = $this->getMotivTable()
                    ->saveMotiv($motiv);
                
                $page = '1';
                
                if ($this->session->offsetExists('page'))
                {
                    $page = $this->session->offsetGet('page') . '#' . $id;
                }
                
                return $this->redirect()
                    ->toUrl('/motiv?page=' . $page);
            }
        }
        
        return array(
                'form' => $form
        );
    }

    public function editAction()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('MotivController->editAction()');
        
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('motiv', array(
                    'action' => 'add'
            ));
        }
        
        $motiv = '';
        
        // Get the Motiv with the specified id. An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try
        {
            $motiv = $this->getMotivTable()
                ->getMotiv($id);
        }
        catch (\Exception $ex)
        {
            $firephp->error('MotivController->editAction()');
            
            return $this->redirect()
                ->toRoute('motiv', array(
                    'action' => 'index'
            ));
        }
        
        $firephp->log('MotivController->editAction(). $motiv=' . $motiv->motiv . '. $ortid=' . $motiv->ortid);
        
        $form = new MotivForm($this->getOrtTable()
            ->fetchAll());
        $form->bind($motiv);
        $form->get('submit')
            ->setValue('Ändern');
        $form->get('ortid')
            ->setValue($motiv->ortid);
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $firephp->log('isPost -> MotivController->editAction()');
            
            $form->setInputFilter($motiv->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid())
            {
                $firephp->log('isValid -> MotivController->editAction()');
                
                $this->session->offsetSet('input_ortid', $motiv->ortid);
                
                $this->getMotivTable()
                    ->saveMotiv($motiv);
                
                $page = '1';
                
                if ($this->session->offsetExists('page'))
                {
                    $page = $this->session->offsetGet('page') . '#' . $id;
                }
                
                return $this->redirect()
                    ->toUrl('/motiv?page=' . $page);
            }
            
            $firephp->warn('isValid=false -> MotivController->editAction()');
        }
        
        return array(
                'id' => $id,
                'form' => $form
        );
    }

    public function deleteAction()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('MotivController->deleteAction()');
        
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('motiv');
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $del = $request->getPost('del', 'Nein');
            
            if ($del == 'Ja')
            {
                $id = (int) $request->getPost('id');
                $this->getMotivTable()
                    ->deleteMotiv($id);
            }
            
            // Redirect to list of motivs
            return $this->redirect()
                ->toRoute('motiv');
        }
        
        return array(
                'id' => $id,
                'motiv' => $this->getMotivTable()
                    ->getMotiv($id)
        );
    }

    public function getMotivTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('MotivController->getMotivTable()');
        
        if (! $this->motivTable)
        {
            $sm = $this->getServiceLocator();
            $this->motivTable = $sm->get('Motiv\Model\MotivTable');
        }
        
        return $this->motivTable;
    }

    public function getOrtTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('MotivController->getOrtTable()');
        
        if (! $this->ortTable)
        {
            $sm = $this->getServiceLocator();
            $this->ortTable = $sm->get('Motiv\Model\OrtTable');
        }
        
        return $this->ortTable;
    }
}
