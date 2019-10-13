<?php
namespace Serie\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class SerieBildTable extends AbstractTableGateway
{
    protected $table = 'ofa_serie_bild';

    public function __construct(Adapter $adapter)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('SerieBildTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new SerieBild());

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('SerieBildTable->fetchAll()');
 
    	if (null === $select)
            $select = new Select();
            
        $select->from($this->table)
            ->columns(array('id', 'serieid', 'nr', 'bildid'))
        	->order('serieid ASC, nr ASC');
        
        // echo $select->getSqlString();
        
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        
        return $resultSet;
    }

    public function getSerieBild($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('SerieBildTable->getSerieBild()');
 
    	$id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row)
        {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function deleteSerieBild($serieid)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('SerieTable->deleteSerieBild()');
        
        $this->delete(array(
                'serieid' => (int) $serieid
        ));
    }
}
