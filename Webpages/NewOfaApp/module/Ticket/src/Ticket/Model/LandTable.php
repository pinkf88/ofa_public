<?php
namespace Land\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class LandTable extends AbstractTableGateway
{
    protected $table = 'land';

    public function __construct(Adapter $adapter)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('LandTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Land());

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('LandTable->fetchAll()');
 
    	if (null === $select)
            $select = new Select();
            
        $select->from($this->table)
            ->columns(array('id', 'land', 'kurz', 'kontinentid'))
            ->join('kontinent', 'land.kontinentid = kontinent.id', 'kontinent');
        
        // echo $select->getSqlString();
        
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        
        return $resultSet;
    }

    public function getLand($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('LandTable->getLand()');
 
    	$id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        
        $firephp->log('LandTable->getLand(). $id=' . $id . '. $land=' . $row->land . '. $kontinentid=' . $row->kontinentid);

        if (!$row)
        {
            throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function getKontinentID($id)
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('LandTable->getKontinentID()');
    
    	$id  = (int) $id;
    	$rowset = $this->select(array('id' => $id));
		$row = $rowset->current();
    
		if (!$row)
		{
			$firephp->error('LandTable->getKontinentID(). Could not find row ' . $id);
  			throw new \Exception("Could not find row $id");
		}

		return (int)$row->kontinentid;
 	}

    public function saveLand(Land $land)
    {
		$firephp = \FirePHP::getInstance(true);
 
    	$data = array(
            'land' => $land->land,
            'kurz'  => $land->kurz,
            'kontinentid'  => $land->kontinentid,
    	);

        $id = (int) $land->id;
        
		$firephp->log('LandTable->saveLand(). ' . $id . "/" . $land->land . "/" . $land->kurz . "/" . $land->kontinentid);
        
        if ($id == 0)
        {
            $this->insert($data);
        }
        else
        {
            if ($this->getLand($id))
            {
                $this->update($data, array('id' => $id));
            }
            else
            {
            	$firephp->error('LandTable->saveLand(). Land id does not exist');
                throw new \Exception('Land id does not exist');
            }
        }
    }

    public function deleteLand($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('LandTable->deleteLand()');
 
    	$this->delete(array('id' => (int) $id));
    }
}
