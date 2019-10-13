<?php
namespace Bild\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class BildMotivTable extends AbstractTableGateway
{
    protected $table = 'ofa_bild_motiv';

    public function __construct(Adapter $adapter)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('BildMotivTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new BildMotiv());

        $this->initialize();
    }

    public function fetchByBild($bildid, Select $select = null)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('BildMotivTable->fetchByBild()');
 
    	if (null === $select)
            $select = new Select();
            
        $select->from($this->table)
            ->columns(array('id', 'motivid'))
	    	->where('bildid=' . $bildid)
            ->order('motivid ASC');
        
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        
        return $resultSet;
    }

    public function getBildMotiv($id)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('BildMotivTable->getBildMotiv()');
 
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
