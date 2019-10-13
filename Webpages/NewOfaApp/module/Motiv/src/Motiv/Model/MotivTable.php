<?php
namespace Motiv\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;


class MotivTable extends AbstractTableGateway
{
    protected $table = 'ofa_motiv';

    public function __construct(Adapter $adapter)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('MotivTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Motiv());

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
		$firephp = \FirePHP::getInstance(true);

		if (null === $select)
			$select = new Select();
		
		$select->from($this->table)
			->columns(array('id', 'motiv', 'ortid', 'laenge', 'breite', 'link', 'mapzoom'))
			->join('ofa_ort', 'ofa_motiv.ortid = ofa_ort.id', array('ort', 'ortlaenge' => 'laenge', 'ortbreite' => 'breite'));
		
		$firephp->log('MotivTable->fetchAll(). SQL: ' . $select->getSqlString());

   		$paginatorAdapter = new DbSelect(
   				$select,
				$this->adapter,
				$this->resultSetPrototype
   		);
    		
   		$paginator = new Paginator($paginatorAdapter);
   		return $paginator;
    }

    public function getMotiv($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('MotivTable->getMotiv()');
 
    	$id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        
        $firephp->log('MotivTable->getMotiv(). $id=' . $id . '. $motiv=' . $row->motiv . '. $ortid=' . $row->ortid);

        if (!$row)
        {
			$firephp->error('MotivTable->getMotiv(). Could not find row ' . $id);
        	throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function getOrtID($id)
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('MotivTable->getOrtID()');
    
    	$id  = (int) $id;
    	$rowset = $this->select(array('id' => $id));
		$row = $rowset->current();
    
		if (!$row)
		{
			$firephp->error('MotivTable->getOrtID(). Could not find row ' . $id);
  			throw new \Exception("Could not find row $id");
		}

		return (int)$row->ortid;
 	}

    public function saveMotiv(Motiv $motiv)
    {
		$firephp = \FirePHP::getInstance(true);
 
    	$data = array(
            'motiv' => $motiv->motiv,
            'ortid' => $motiv->ortid,
            'laenge' => $motiv->laenge,
            'breite' => $motiv->breite,
    	    'link' => $motiv->link,
            'mapzoom' => $motiv->mapzoom,
    	);

        $id = (int) $motiv->id;
        
		$firephp->log('MotivTable->saveMotiv(). ' . $id . "/" . $motiv->motiv . "/" . $motiv->ortid . "/" . $motiv->laenge . "/" . $motiv->breite);
        
        if ($id == 0)
        {
            $this->insert($data);
            $id = $this->lastInsertValue;
        }
        else
        {
            if ($this->getMotiv($id))
            {
                $this->update($data, array('id' => $id));
            }
            else
            {
            	$firephp->error('MotivTable->saveMotiv(). Motiv id does not exist');
                throw new \Exception('Motiv id does not exist');
            }
        }
        
        return $id;
    }

    public function deleteMotiv($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('MotivTable->deleteMotiv()');
 
    	$this->delete(array('id' => (int) $id));
    }
}
