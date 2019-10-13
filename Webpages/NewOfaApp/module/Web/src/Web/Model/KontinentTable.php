<?php
namespace Web\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class KontinentTable extends AbstractTableGateway
{
    protected $table = 'ofa_kontinent';

    public function __construct(Adapter $adapter)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('KontinentTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Kontinent());

        $this->initialize();
    }

    public function getKontinent($id)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('KontinentTable->getKontinent()');
 
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
