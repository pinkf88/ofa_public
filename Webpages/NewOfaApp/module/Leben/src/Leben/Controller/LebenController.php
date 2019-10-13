<?php

namespace Leben\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Leben\Model\Leben;
use Leben\Form\LebenForm;
use Leben\Form\LebenSelectForm;

class LebenController extends AbstractActionController
{
    protected $lebenTable;
    protected $ortTable;
    protected $landTable;
    protected $session;

    public function __construct()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenController->__construct().');
        
        $this->session = new Container('ofa_leben');
    }

    public function indexAction()
    {
        $firephp = \FirePHP::getInstance(true);
        
        $jahr = 0;
        $ortid = 0;
        $landid = 0;
        $suchtext = '';
        $countperpage = 100;
        
        if ($this->session->offsetExists('jahr'))
        {
            $jahr = intval($this->session->offsetGet('jahr'));
        }
        
        if ($this->session->offsetExists('ortid'))
        {
            $ortid = intval($this->session->offsetGet('ortid'));
        }
        
        if ($this->session->offsetExists('landid'))
        {
            $landid = intval($this->session->offsetGet('landid'));
        }
        
        if ($this->session->offsetExists('suchtext'))
        {
            $suchtext = $this->session->offsetGet('suchtext');
        }
        
        if ($this->session->offsetExists('countperpage'))
        {
            $countperpage = intval($this->session->offsetGet('countperpage'));
        }
        
        if ($this->getRequest())
        {
            $firephp->log('LebenController->indexAction(). getRequest(): isPost=' . $this->getRequest()
                ->isPost());
            
            if ($this->getRequest()
                ->isPost())
            {
                if ($this->getRequest()
                    ->getPost('jahr'))
                {
                    $firephp->log('LebenController->indexAction(). jahr=' . $this->getRequest()
                        ->getPost('jahr'));
                    $jahr = intval($this->getRequest()
                        ->getPost('jahr'));
                }
                else
                {
                    $jahr = 0;
                }
                
                if ($this->getRequest()
                    ->getPost('ortid'))
                {
                    $firephp->log('LebenController->indexAction(). ortid=' . $this->getRequest()
                        ->getPost('ortid'));
                    $ortid = intval($this->getRequest()
                        ->getPost('ortid'));
                }
                else
                {
                    $ortid = 0;
                }
                
                if ($this->getRequest()
                    ->getPost('landid'))
                {
                    $firephp->log('LebenController->indexAction(). landid=' . $this->getRequest()
                        ->getPost('landid'));
                    $landid = intval($this->getRequest()
                        ->getPost('landid'));
                }
                else
                {
                    $landid = 0;
                }
                
                if ($this->getRequest()
                    ->getPost('suchtext'))
                {
                    $firephp->log('BildController->indexAction(). suchtext=' . $this->getRequest()
                        ->getPost('suchtext'));
                    $suchtext = $this->getRequest()
                        ->getPost('suchtext');
                }
                else
                {
                    $suchtext = "";
                }
                
                if ($this->getRequest()
                    ->getPost('countperpage'))
                {
                    $firephp->log('LebenController->indexAction(). countperpage=' . $this->getRequest()
                        ->getPost('countperpage'));
                    $countperpage = intval($this->getRequest()
                        ->getPost('countperpage'));
                }
                else
                {
                    $countperpage = 100;
                }
            }
        }
        
        $this->session->offsetSet('jahr', $jahr);
        $this->session->offsetSet('ortid', $ortid);
        $this->session->offsetSet('landid', $landid);
        $this->session->offsetSet('suchtext', $suchtext);
        $this->session->offsetSet('countperpage', $countperpage);
        
        $select = new Select();
        $select->order('jahr DESC')
            ->order('datumvon ASC')
            ->order('nr ASC');
        
        if ($jahr > 0)
        {
            $select->where('YEAR(ofa_leben.datumvon)="' . $jahr . '"');
        }
        
        if ($ortid > 0)
        {
            $select->where('ortid=' . $ortid);
        }
        
        if ($landid > 0)
        {
            $select->where('landid=' . $landid);
        }
        
        if (strlen($suchtext) > 0)
        {
            $select->where('(beschreibung LIKE "%' . $suchtext . '%" OR bemerkung LIKE "%' . $suchtext . '%")');
        }
        
        $firephp->log('LebenController->indexAction(). ' . $jahr . '/' . $ortid . '/' . $landid);
        
        $paginator = $this->getLebenTable()
            ->fetchAll($select);
        $paginator->setCurrentPageNumber((int) $this->params()
            ->fromQuery('page', 1));
        $paginator->setItemCountPerPage($countperpage);
        
        $this->session->offsetSet('page', $this->params()
            ->fromQuery('page', 1));
        
        $selectform = new LebenSelectForm($this->getLebenTable()
            ->fetchAllYears(), $this->getOrtTable()
            ->fetchAll(), $this->getLandTable()
            ->fetchAll());
        
        if ($jahr > 0)
        {
            $selectform->get('jahr')
                ->setValue($jahr);
        }
        
        if ($ortid > 0)
        {
            $selectform->get('ortid')
                ->setValue($ortid);
        }
        
        if ($landid > 0)
        {
            $selectform->get('landid')
                ->setValue($landid);
        }
        
        if (strlen($suchtext) > 0)
        {
            $selectform->get('suchtext')
                ->setValue($suchtext);
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
        $firephp->log('LebenController->addAction()');
        // $firephp->trace('Trace Label');
        
        $session = $this->session;
        
        $form = new LebenForm($this->getOrtTable()
            ->fetchAll());
        $form->get('submit1')
            ->setValue('Ok');
        $form->get('submit2')
            ->setValue('Ok / Neu');
        $form->get('submit3')
            ->setValue('Ok / Neu+1');
        
        if ($session->offsetExists('input_datumvon'))
        {
            $form->get('datumvon')
                ->setValue($session->offsetGet('input_datumvon'));
        }
        
        if ($session->offsetExists('input_datumbis'))
        {
            $form->get('datumbis')
                ->setValue($session->offsetGet('input_datumbis'));
        }
        
        if ($session->offsetExists('input_ortid'))
        {
            $form->get('ortid')
                ->setValue($session->offsetGet('input_ortid'));
        }
        
        if ($session->offsetExists('input_beschreibung'))
        {
            $form->get('beschreibung')
                ->setValue($session->offsetGet('input_beschreibung'));
        }
        
        if ($session->offsetExists('input_bemerkung'))
        {
            $form->get('bemerkung')
                ->setValue($session->offsetGet('input_bemerkung'));
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $leben = new Leben();
            $form->setInputFilter($leben->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid())
            {
                $leben->exchangeArray($form->getData());
                
                $session->offsetSet('input_datumvon', $leben->datumvon);
                $session->offsetSet('input_datumbis', $leben->datumbis);
                $session->offsetSet('input_ortid', $leben->ortid);
                $session->offsetSet('input_beschreibung', $leben->beschreibung);
                $session->offsetSet('input_bemerkung', $leben->bemerkung);
                
                $id = $this->getLebenTable()
                    ->saveLeben($leben);
                
                if ($request->getPost('submit1'))
                {
                    $page = '1';
                    
                    // Redirect to list of orts
                    if ($this->session->offsetExists('page'))
                    {
                        $page = $this->session->offsetGet('page') . '#' . $id;
                    }
                    
                    return $this->redirect()
                        ->toUrl('/leben?page=' . $page);
                }
                
                if ($request->getPost('submit3'))
                {
                    $form->get('datumvon')
                        ->setValue(date('Y-m-d', strtotime('+1 day', strtotime($form->get('datumvon')
                        ->getValue()))));
                    
                    $form->get('datumbis')
                        ->setValue(date('Y-m-d', strtotime('+1 day', strtotime($form->get('datumbis')
                        ->getValue()))));
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
        $firephp->log('LebenController->editAction()');
        
        $session = $this->session;
        
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('leben', array(
                    'action' => 'add'
            ));
        }
        
        $leben = '';
        
        // Get the Leben with the specified id. An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try
        {
            $leben = $this->getLebenTable()
                ->getLeben($id);
        }
        catch (\Exception $ex)
        {
            $firephp->error('LebenController->editAction()');
            
            return $this->redirect()
                ->toRoute('leben', array(
                    'action' => 'index'
            ));
        }
        
        $firephp->log('LebenController->editAction(). $nr=' . $leben->nr . '. $ortid=' . $leben->ortid . '. $beschreibung=' . $leben->beschreibung);
        
        $form = new LebenForm($this->getOrtTable()
            ->fetchAll());
        $form->bind($leben);
        $form->get('submit1')
            ->setValue('Ändern');
        $form->get('submit2')
            ->setValue('');
        $form->get('ortid')
            ->setValue($leben->ortid);
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $firephp->log('isPost -> LebenController->editAction()');
            $firephp->log('LebenController->editAction(). $nr=' . $leben->nr . '. $ortid=' . $leben->ortid . '. $beschreibung=' . $leben->beschreibung);
            
            $form->setInputFilter($leben->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid())
            {
                $firephp->log('isValid -> LebenController->editAction()');
                $firephp->log('LebenController->editAction(). $nr=' . $leben->nr . '. $ortid=' . $leben->ortid . '. $beschreibung=' . $leben->beschreibung);
                
                $session->offsetSet('input_datumvon', $leben->datumvon);
                $session->offsetSet('input_datumbis', $leben->datumbis);
                $session->offsetSet('input_ortid', $leben->ortid);
                $session->offsetSet('input_beschreibung', $leben->beschreibung);
                $session->offsetSet('input_bemerkung', $leben->bemerkung);
                
                $this->getLebenTable()
                    ->saveLeben($leben);
                
                $page = '1';
                
                // Redirect to list of orts
                if ($this->session->offsetExists('page'))
                {
                    $page = $this->session->offsetGet('page') . '#' . $id;
                }
                
                return $this->redirect()
                    ->toUrl('/leben?page=' . $page);
            }
            
            $firephp->warn('isValid=false -> LebenController->editAction()');
        }
        
        return array(
                'id' => $id,
                'form' => $form
        );
    }

    public function deleteAction()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenController->deleteAction()');
        
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('leben');
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $del = $request->getPost('del', 'Nein');
            
            if ($del == 'Ja')
            {
                $id = (int) $request->getPost('id');
                $this->getLebenTable()
                    ->deleteLeben($id);
            }
            
            // Redirect to list of lebens
            return $this->redirect()
                ->toRoute('leben');
        }
        
        return array(
                'id' => $id,
                'leben' => $this->getLebenTable()
                    ->getLeben($id)
        );
    }

    public function getLebenTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenController->getLebenTable()');
        
        if (! $this->lebenTable)
        {
            $sm = $this->getServiceLocator();
            $this->lebenTable = $sm->get('Leben\Model\LebenTable');
        }
        
        return $this->lebenTable;
    }

    public function getOrtTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenController->getOrtTable()');
        
        if (! $this->ortTable)
        {
            $sm = $this->getServiceLocator();
            $this->ortTable = $sm->get('Leben\Model\OrtTable');
        }
        
        return $this->ortTable;
    }

    public function getLandTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenController->getLandTable()');
        
        if (! $this->landTable)
        {
            $sm = $this->getServiceLocator();
            $this->landTable = $sm->get('Leben\Model\LandTable');
        }
        
        return $this->landTable;
    }
}
