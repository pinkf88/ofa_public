<?php
namespace Ort\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;


class OrtTable extends AbstractTableGateway
{
    protected $table = 'ofa_ort';

    public function __construct(Adapter $adapter)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('OrtTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Ort($adapter));

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
		$firephp = \FirePHP::getInstance(true);

		if (null === $select)
			$select = new Select();
		
		$select->from($this->table)
			->columns(array('id', 'ort', 'landid', 'laenge', 'breite'))
			->join('ofa_land', 'ofa_ort.landid = ofa_land.id', 'land');
		
		$firephp->log('OrtTable->fetchAll(). SQL: ' . $select->getSqlString());

   		$paginatorAdapter = new DbSelect(
   				$select,
				$this->adapter,
				$this->resultSetPrototype
   		);
    		
   		$paginator = new Paginator($paginatorAdapter);
   		return $paginator;
    }

    public function getOrt($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('OrtTable->getOrt()');
 
    	$id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        
        $firephp->log('OrtTable->getOrt(). $id=' . $id . '. $ort=' . $row->ort . '. $landid=' . $row->landid);

        if (!$row)
        {
			$firephp->error('OrtTable->getOrt(). Could not find row ' . $id);
        	throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function getLandID($id)
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('OrtTable->getLandID()');
    
    	$id  = (int) $id;
    	$rowset = $this->select(array('id' => $id));
		$row = $rowset->current();
    
		if (!$row)
		{
			$firephp->error('OrtTable->getLandID(). Could not find row ' . $id);
  			throw new \Exception("Could not find row $id");
		}

		return (int)$row->landid;
 	}

    public function saveOrt(Ort $ort)
    {
		$firephp = \FirePHP::getInstance(true);
 
    	$data = array(
            'ort' => $ort->ort,
            'landid' => $ort->landid,
            'laenge' => $ort->laenge,
            'breite' => $ort->breite,
    	);

        $id = (int) $ort->id;
        
		$firephp->log('OrtTable->saveOrt(). ' . $id . "/" . $ort->ort . "/" . $ort->landid . "/" . $ort->laenge . "/" . $ort->breite);
        
        if ($id == 0)
        {
            $this->insert($data);
        }
        else
        {
            if ($this->getOrt($id))
            {
                $this->update($data, array('id' => $id));
            }
            else
            {
            	$firephp->error('OrtTable->saveOrt(). Ort id does not exist');
                throw new \Exception('Ort id does not exist');
            }
        }
    }

    public function deleteOrt($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('OrtTable->deleteOrt()');
 
    	$this->delete(array('id' => (int) $id));
    }
}
