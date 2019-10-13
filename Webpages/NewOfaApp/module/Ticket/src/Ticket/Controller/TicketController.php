<?php
namespace Ticket\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Ticket\Model\Ticket;
use Ticket\Model\Kontinent;
use Ticket\Form\TicketForm;

class TicketController extends AbstractActionController
{
    protected $ticketTable;
    protected $kontinentTable;
    
    public function indexAction()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('TicketController->indexAction()');
    	
    	$select = new Select();

        $order_by = $this->params()->fromRoute('order_by') ?
                $this->params()->fromRoute('order_by') : 'ticket';
        $order = $this->params()->fromRoute('order') ?
                $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;

        $select->order($order_by . ' ' . $order);

        return new ViewModel(array(
                    'tickets' => $this->getTicketTable()->fetchAll($select),
                    'order_by' => $order_by,
                    'order' => $order,
                ));
    }

    public function addAction()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('TicketController->addAction()');
    	// $firephp->trace('Trace Label');
    	
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    	$form = new TicketForm(null, $dbAdapter);
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $ticket = new Ticket();
            $form->setInputFilter($ticket->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                $ticket->exchangeArray($form->getData());
                $this->getTicketTable()->saveTicket($ticket);

                // Redirect to list of tickets
                return $this->redirect()->toRoute('ticket');
            }
        }
        
        return array(
                'form' => $form,
            );
    }

    public function editAction()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('TicketController->editAction()');
    	
    	$id = (int) $this->params()->fromRoute('id', 0);

        if (!$id)
        {
            return $this->redirect()->toRoute('ticket', array(
                'action' => 'add'
            ));
        }

        $ticket = '';
        
        // Get the Ticket with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try
        {
            $ticket = $this->getTicketTable()->getTicket($id);
        }
        catch (\Exception $ex)
        {
        	$firephp->error('TicketController->editAction()');
        	
            return $this->redirect()->toRoute('ticket', array(
                'action' => 'index'
            ));
        }

        $firephp->log('TicketController->editAction(). $ticket=' . $ticket->ticket . '. $kontinentid=' . $ticket->kontinentid);
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form  = new TicketForm(null, $dbAdapter);
        $form->bind($ticket);
        $form->get('submit')->setAttribute('value', 'Edit');
        $form->get('kontinentid')->setValue($ticket->kontinentid);
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
        	$firephp->log('isPost -> TicketController->editAction()');
        	
            $form->setInputFilter($ticket->getInputFilter());
            $form->setData($request->getPost());
            
            print_r($request->getPost());

            if ($form->isValid())
            {
        		$firephp->log('isValid -> TicketController->editAction()');
            	
        		$this->getTicketTable()->saveTicket($ticket);

                // Redirect to list of tickets
                return $this->redirect()->toRoute('ticket');
            }
            
            $firephp->warn('isValid=false -> TicketController->editAction()');
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('TicketController->deleteAction()');
    	
    	$id = (int) $this->params()->fromRoute('id', 0);
        
        if (!$id)
        {
            return $this->redirect()->toRoute('ticket');
        }

        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $del = $request->getPost('del', 'Nein');

            if ($del == 'Ja')
            {
                $id = (int) $request->getPost('id');
                $this->getTicketTable()->deleteTicket($id);
            }

            // Redirect to list of tickets
            return $this->redirect()->toRoute('ticket');
        }

        return array(
            'id'    => $id,
            'ticket' => $this->getTicketTable()->getTicket($id)
        );
    }

    public function getTicketTable()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('TicketController->getTicketTable()');
    	
    	if (!$this->ticketTable)
        {
            $sm = $this->getServiceLocator();
            $this->ticketTable = $sm->get('Ticket\Model\TicketTable');
        }

        return $this->ticketTable;
    }

    public function getKontinentTable()
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('TicketController->getKontinentTable()');
    	
    	if (!$this->kontinentTable)
        {
            $sm = $this->getServiceLocator();
            $this->kontinentTable = $sm->get('Ticket\Model\KontinentTable');
        }

        return $this->kontinentTable;
    }
}
