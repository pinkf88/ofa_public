<?php
namespace Web\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class WebSerieTable extends AbstractTableGateway
{
    protected $table = 'ofa_web_serie';

    public function __construct(Adapter $adapter)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('WebSerieTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new WebSerie());

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('WebSerieTable->fetchAll()');
 
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

    public function getWebSerie($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('WebSerieTable->getWebSerie()');
 
    	$id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row)
        {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function deleteWebSerie($webid)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebSerieTable->deleteWebSerie()');
        
        $this->delete(array(
                'webid' => (int) $webid
        ));
    }
}
