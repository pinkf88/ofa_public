<?php
namespace Bild\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class SerieTable extends AbstractTableGateway
{
    protected $table = 'ofa_serie';

    public function __construct(Adapter $adapter)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('SerieTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Serie());

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('SerieTable->fetchAll()');
 
    	if (null === $select)
            $select = new Select();
            
        $select->from($this->table)
            ->columns(array('id', 'serie'))
        	->order('serie ASC');
        
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        
        return $resultSet;
    }

    public function getSerie($id)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('SerieTable->getSerie()');
 
    	$id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row)
        {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
}
