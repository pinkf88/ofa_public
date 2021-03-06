<?php

namespace Serie\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Serie\Model\Serie;
use Serie\Form\SerieForm;
use Serie\Form\SerieSelectForm;

class SerieController extends AbstractActionController
{
    protected $serieTable;
    protected $serieBildTable;
    protected $webTable;
    protected $session;

    public function __construct()
    {
        $this->session = new Container('ofa_serie');
    }

    public function indexAction()
    {
        $suchtext = '';
        $countperpage = 250;
        $webid = 0;
        
        if ($this->session->offsetExists('suchtext'))
        {
            $suchtext = $this->session->offsetGet('suchtext');
        }
        
        if ($this->session->offsetExists('countperpage'))
        {
            $countperpage = intval($this->session->offsetGet('countperpage'));
        }
        
        if ($this->session->offsetExists('webid'))
        {
            $webid = intval($this->session->offsetGet('webid'));
        }
        
        if ($this->getRequest())
        {
            if ($this->getRequest()
                ->isPost())
            {
                if ($this->getRequest()
                    ->getPost('suchtext'))
                {
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
                    $countperpage = intval($this->getRequest()
                        ->getPost('countperpage'));
                }
                else
                {
                    $countperpage = 250;
                }
                
                if ($this->getRequest()
                    ->getPost('webid'))
                {
                    $webid = intval($this->getRequest()
                        ->getPost('webid'));
                }
                else
                {
                    $webid = 0;
                }
            }
        }
        
        $this->session->offsetSet('suchtext', $suchtext);
        $this->session->offsetSet('countperpage', $countperpage);
        $this->session->offsetSet('webid', $webid);
        
        $select = new Select();
        
        if (strlen($suchtext) > 0)
        {
            $select->where('serie LIKE "%' . $suchtext . '%"');
        }
        
        $select->order('serie ' . Select::ORDER_ASCENDING);
        
        $paginator = $this->getSerieTable()
            ->fetchAll($select);
        $paginator->setCurrentPageNumber((int) $this->params()
            ->fromQuery('page', 1));
        $paginator->setItemCountPerPage($countperpage);
        
        $this->session->offsetSet('page', $this->params()
            ->fromQuery('page', 1));
        
        $selectform = new SerieSelectForm($this->getWebTable()
            ->fetchAll());
        
        if (strlen($suchtext) > 0)
        {
            $selectform->get('suchtext')
                ->setValue($suchtext);
        }
        
        $selectform->get('countperpage')
            ->setValue($countperpage);
        
        if ($webid > 0)
        {
            $selectform->get('webid')
                ->setValue($webid);
        }
        
        return new ViewModel(array(
                'paginator' => $paginator,
                'selectform' => $selectform
        ));
    }

    public function addAction()
    {
        $session = $this->session;
        
        $form = new SerieForm();
        $form->get('submit')
            ->setValue('Ok');
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $serie = new Serie($this->getServiceLocator()
                ->get('Zend\Db\Adapter\Adapter'));
            
            $form->setInputFilter($serie->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid())
            {
                $serie->exchangeArray($form->getData());
                
                $this->getSerieTable()
                    ->saveSerie($serie);
                
                if ($request->getPost('submit'))
                    return $this->redirect()
                        ->toRoute('serie');
            }
        }
        
        return array(
                'form' => $form
        );
    }

    public function editAction()
    {
        $session = $this->session;
        
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('serie', array(
                    'action' => 'add'
            ));
        }
        
        $serie = '';
        
        // Get the Serie with the specified id. An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try
        {
            $serie = $this->getSerieTable()
                ->getSerie($id);
        }
        catch (\Exception $ex)
        {
            return $this->redirect()
                ->toRoute('serie', array(
                    'action' => 'index'
            ));
        }
        
        $form = new SerieForm();
        $form->bind($serie);
        $form->get('submit')
            ->setValue('Ändern');
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $form->setInputFilter($serie->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid())
            {
                $this->getSerieTable()
                    ->saveSerie($serie);
                
                $page = '1';
                
                // Redirect to list of series
                if ($this->session->offsetExists('page'))
                {
                    $page = $this->session->offsetGet('page') . '#' . $id;
                }
                
                return $this->redirect()
                    ->toUrl('/serie?page=' . $page);
            }
        }
        
        return array(
                'id' => $id,
                'form' => $form
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('serie');
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $del = $request->getPost('del', 'Nein');
            
            if ($del == 'Ja')
            {
                $id = (int) $request->getPost('id');
                
                $this->getSerieTable()
                    ->deleteSerie($id);
                
                $this->getSerieBildTable()
                    ->deleteSerieBild($id);
            }
            
            // Redirect to list of series
            return $this->redirect()
                ->toRoute('serie');
        }
        
        return array(
                'id' => $id,
                'serie' => $this->getSerieTable()
                    ->getSerie($id)
        );
    }

    public function getSerieTable()
    {
        if (! $this->serieTable)
        {
            $sm = $this->getServiceLocator();
            $this->serieTable = $sm->get('Serie\Model\SerieTable');
        }
        
        return $this->serieTable;
    }

    public function getSerieBildTable()
    {
        if (! $this->serieBildTable)
        {
            $sm = $this->getServiceLocator();
            $this->serieBildTable = $sm->get('Serie\Model\SerieBildTable');
        }
        
        return $this->serieBildTable;
    }

    public function getWebTable()
    {
        if (! $this->webTable)
        {
            $sm = $this->getServiceLocator();
            $this->webTable = $sm->get('Serie\Model\WebTable');
        }
        
        return $this->webTable;
    }
}
