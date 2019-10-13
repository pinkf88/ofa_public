<?php

namespace Label\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Validator\Db\NoRecordExists;
use Label\Model\Label;
use Label\Form\LabelForm;
use Label\Form\LabelSelectForm;
use Zend\Session\Container;

class LabelController extends AbstractActionController
{
    protected $labelTable;
    protected $session;

    public function __construct()
    {
        $this->session = new Container('ofa_label');
    }

    public function indexAction()
    {
        $select = new Select();
        
        $select->order('label_en ' . Select::ORDER_ASCENDING);
        
        $paginator = $this->getLabelTable()
            ->fetchAll($select);
        $paginator->setCurrentPageNumber((int) $this->params()
            ->fromQuery('page', 1));
        $paginator->setItemCountPerPage(1000000);
        
        $selectform = new LabelSelectForm();
        
        return new ViewModel(array(
                'paginator' => $paginator,
                'selectform' => $selectform
        ));
    }

    public function addAction()
    {
        $dbAdapter = $this->getServiceLocator()
            ->get('Zend\Db\Adapter\Adapter');
        
        $form = new LabelForm(null, $dbAdapter);
        $form->get('submit')
            ->setValue('Ok');
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $label = new Label($dbAdapter);
            $form->setInputFilter($label->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid())
            {
                $label->exchangeArray($form->getData());
                
                $validator = new NoRecordExists(array(
                        'adapter' => $dbAdapter,
                        'table' => 'ofa_label',
                        'field' => 'label'
                ));
                
                if ($validator->isValid($label->label))
                {
                    $this->getLabelTable()
                        ->saveLabel($label);
                    
                    // Redirect to list of labels
                    return $this->redirect()
                        ->toRoute('label');
                }
            }
        }
        
        return array(
                'form' => $form
        );
    }

    public function editAction()
    {
        $id = (int) $this->params()
            ->fromRoute('id', 0);
        
        if (! $id)
        {
            return $this->redirect()
                ->toRoute('label', array(
                    'action' => 'add'
            ));
        }
        
        $label = '';
        
        // Get the Label with the specified id. An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try
        {
            $label = $this->getLabelTable()
                ->getLabel($id);
        }
        catch (\Exception $ex)
        {
            return $this->redirect()
                ->toRoute('label', array(
                    'action' => 'index'
            ));
        }
        
        $dbAdapter = $this->getServiceLocator()
            ->get('Zend\Db\Adapter\Adapter');
        
        $form = new LabelForm(null, $dbAdapter);
        $form->bind($label);
        
        $form->get('submit')
            ->setValue('Ändern');
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $form->setInputFilter($label->getInputFilter());
            $form->setData($request->getPost());
            
            // print_r($request->getPost());
            
            if ($form->isValid())
            {
                $this->getLabelTable()
                    ->saveLabel($label);
                
                // Redirect to list of labels
                return $this->redirect()
                    ->toRoute('label');
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
                ->toRoute('label');
        }
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $del = $request->getPost('del', 'Nein');
            
            if ($del == 'Ja')
            {
                $id = (int) $request->getPost('id');
                $this->getLabelTable()
                    ->deleteLabel($id);
            }
            
            // Redirect to list of labels
            return $this->redirect()
                ->toRoute('label');
        }
        
        return array(
                'id' => $id,
                'label' => $this->getLabelTable()
                    ->getLabel($id)
        );
    }

    public function getLabelTable()
    {
        if (! $this->labelTable)
        {
            $sm = $this->getServiceLocator();
            $this->labelTable = $sm->get('Label\Model\LabelTable');
        }
        
        return $this->labelTable;
    }
}
