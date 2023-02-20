<?php
namespace Leben\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class OrtTable extends AbstractTableGateway
{
    protected $table = 'ofa_ort';

    public function __construct(Adapter $adapter)
    {
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Ort());

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
    	if (null === $select) {
            $select = new Select();
        }

        $select->from($this->table)
            ->columns(array('id', 'ort'))
        	->order('ort ASC');

        // echo $select->getSqlString();

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    public function getOrt($id)
    {
    	$id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row->ort;
    }
}
