<?php
namespace Label\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class LabelTable extends AbstractTableGateway
{
    protected $table = 'ofa_label';

    public function __construct(Adapter $adapter)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('LabelTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Label($adapter));

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('LabelTable->fetchAll()');
 
    	if (null === $select)
            $select = new Select();
            
        $select->from($this->table)
            ->columns(array('id', 'label_en', 'label_de', 'used'));

        $firephp->log('LabelTable->fetchAll(). SQL: ' . $select->getSqlString());
        
        $paginatorAdapter = new DbSelect(
        		$select,
        		$this->adapter,
        		$this->resultSetPrototype
        );
        
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function getLabel($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('LabelTable->getLabel()');
 
    	$id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row)
        {
            throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function saveLabel(Label $label)
    {
		$firephp = \FirePHP::getInstance(true);
 
    	$data = array(
            'label_en' => $label->label_en,
            'label_de' => $label->label_de,
            'used' => $label->used
    	);

        $id = (int) $label->id;
        
        if ($id == 0)
        {
            $this->insert($data);
        }
        else
        {
            if ($this->getLabel($id))
            {
                $this->update($data, array('id' => $id));
            }
            else
            {
            	$firephp->error('LabelTable->saveLabel(). Label id does not exist');
                throw new \Exception('Label id does not exist');
            }
        }
    }

    public function deleteLabel($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('LabelTable->deleteLabel()');
 
    	$this->delete(array('id' => (int) $id));
    }
}
