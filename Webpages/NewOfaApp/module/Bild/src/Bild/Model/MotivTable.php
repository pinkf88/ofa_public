<?php
namespace Bild\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class MotivTable extends AbstractTableGateway
{
    protected $table = 'ofa_motiv';

    public function __construct(Adapter $adapter)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('MotivTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Motiv());

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('MotivTable->fetchAll()');
 
    	if (null === $select)
            $select = new Select();
            
        $select->from($this->table)
            ->columns(array('id', 'motiv'))
        	->order('motiv ASC');
        
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        
        return $resultSet;
    }

    public function fetchByOrt($ortid, Select $select = null)
    {
    	// $firephp = \FirePHP::getInstance(true);
    	// $firephp->log('MotivTable->fetchAll()');
    
    	if (null === $select)
    		$select = new Select();
    
    	$select->from($this->table)
	    	->columns(array('id', 'motiv'))
	    	->where('ortid=' . $ortid)
	    	->order('motiv ASC');
    
    	$resultSet = $this->selectWith($select);
    	$resultSet->buffer();
    
    	return $resultSet;
    }
    
    public function getMotiv($id)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('MotivTable->getMotiv()');
 
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
