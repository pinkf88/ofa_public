<?php
namespace Bild\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class LandTable extends AbstractTableGateway
{
    protected $table = 'ofa_land';

    public function __construct(Adapter $adapter)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('LandTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Land());

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('LandTable->fetchAll()');
 
    	if (null === $select)
            $select = new Select();
            
        $select->from($this->table)
            ->columns(array('id', 'land'))
        	->order('land ASC');
        
    	// $firephp->log('LandTable->fetchAll(). ' . $select->getSqlString());
                
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        
//         print_r($resultSet);
        
        return $resultSet;
    }

    public function getLand($id)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('LandTable->getLand()');
 
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
