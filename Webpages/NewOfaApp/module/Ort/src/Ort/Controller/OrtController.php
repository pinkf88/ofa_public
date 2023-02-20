<?php

namespace Ort\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Ort\Model\Ort;
use Ort\Model\Land;
use Ort\Form\OrtForm;
use Ort\Form\OrtSelectForm;
use Zend\Session\Container;

class OrtController extends AbstractActionController
{
    protected $ortTable;
    protected $landTable;
    protected $session;

    public function __construct()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('OrtController->__construct().');

        $this->session = new Container('ofa_ort');
    }

    public function indexAction()
    {
        $firephp = \FirePHP::getInstance(true);

        $landid = 0;
        $countperpage = 250;

        if ($this->session->offsetExists('landid'))
        {
            $landid = intval($this->session->offsetGet('landid'));
        }

        if ($this->session->offsetExists('countperpage'))
        {
            $countperpage = intval($this->session->offsetGet('countperpage'));
        }

        if ($this->getRequest())
        {
            $firephp->log('OrtController->indexAction(). getRequest(): isPost=' . $this->getRequest()
                ->isPost());

            if ($this->getRequest()
                ->isPost())
            {
                if ($this->getRequest()
                    ->getPost('landid'))
                {
                    $firephp->log('OrtController->indexAction(). landid=' . $this->getRequest()
                        ->getPost('landid'));
                    $landid = intval($this->getRequest()
                        ->getPost('landid'));
                }
                else
                {
                    $landid = 0;
                }

                if ($this->getRequest()
                    ->getPost('countperpage'))
                {
                    $firephp->log('OrtController->indexAction(). countperpage=' . $this->getRequest()
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

        $this->session->offsetSet('landid', $landid);
        $this->session->offsetSet('countperpage', $countperpage);

        $select = new Select();

        $select->order('ort ' . Select::ORDER_ASCENDING);

        if ($landid > 0)
        {
            $select->where('landid=' . $landid);
        }

        $paginator = $this->getOrtTable()
            ->fetchAll($select);
        $paginator->setCurrentPageNumber((int) $this->params()
            ->fromQuery('page', 1));
        $paginator->setItemCountPerPage($countperpage);

        $this->session->offsetSet('page', $this->params()
            ->fromQuery('page', 1));

        $selectform = new OrtSelectForm($this->getLandTable()
            ->fetchAll());

        if ($landid > 0)
        {
            $selectform->get('landid')
                ->setValue($landid);
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
        $firephp->log('OrtController->addAction()');
        // $firephp->trace('Trace Label');

        $form = new OrtForm($this->getLandTable()
            ->fetchAll());

        $form->get('submit')
            ->setValue('Hinzufügen');

        if ($this->session->offsetExists('input_landid'))
        {
            $form->get('landid')
                ->setValue($this->session->offsetGet('input_landid'));
        }
        else
        {
            if ($this->session->offsetExists('landid'))
            {
                $form->get('landid')
                    ->setValue($this->session->offsetGet('landid'));
            }
        }

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $ort = new Ort($this->getServiceLocator()
                ->get('Zend\Db\Adapter\Adapter'));

            $form->setInputFilter($ort->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $ort->exchangeArray($form->getData());

                $this->session->offsetSet('input_landid', $ort->landid);

                $this->getOrtTable()
                    ->saveOrt($ort);

                // Redirect to list of orts
                return $this->redirect()
                    ->toRoute('ort');
            }
        }

        return array(
                'form' => $form
        );
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (! $id) {
            return $this->redirect()
                ->toRoute('ort', array(
                    'action' => 'add'
            ));
        }

        $ort = '';

        try
        {
            $ort = $this->getOrtTable()->getOrt($id);
        }
        catch (\Exception $ex)
        {
            return $this->redirect()
                ->toRoute('ort', array(
                    'action' => 'index'
            ));
        }

        $form = new OrtForm($this->getLandTable()->fetchAll());
        $form->bind($ort);
        $form->get('submit')->setValue('Ändern');
        $form->get('landid')->setValue($ort->landid);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setInputFilter($ort->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $this->session->offsetSet('input_landid', $ort->landid);

                $this->getOrtTable()->saveOrt($ort);

                $page = '1';

                // Redirect to list of orts
                if ($this->session->offsetExists('page')) {
                    $page = $this->session->offsetGet('page') . '#' . $id;
                }

                return $this->redirect()
                    ->toUrl('/ort?page=' . $page);
            }
        }

        return array(
                'id' => $id,
                'form' => $form
        );
    }

    public function deleteAction()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('OrtController->deleteAction()');

        $id = (int) $this->params()
            ->fromRoute('id', 0);

        if (! $id)
        {
            return $this->redirect()
                ->toRoute('ort');
        }

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $del = $request->getPost('del', 'Nein');

            if ($del == 'Ja')
            {
                $id = (int) $request->getPost('id');
                $this->getOrtTable()
                    ->deleteOrt($id);
            }

            // Redirect to list of orts
            return $this->redirect()
                ->toRoute('ort');
        }

        return array(
                'id' => $id,
                'ort' => $this->getOrtTable()
                    ->getOrt($id)
        );
    }

    public function getOrtTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('OrtController->getOrtTable()');

        if (! $this->ortTable)
        {
            $sm = $this->getServiceLocator();
            $this->ortTable = $sm->get('Ort\Model\OrtTable');
        }

        return $this->ortTable;
    }

    public function getLandTable()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('OrtController->getLandTable()');

        if (! $this->landTable)
        {
            $sm = $this->getServiceLocator();
            $this->landTable = $sm->get('Ort\Model\LandTable');
        }

        return $this->landTable;
    }
}
