<?php
namespace Bild\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class InfoTable extends AbstractTableGateway
{
    protected $table = 'ofa_info';

    public function __construct(Adapter $adapter)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('InfoTable->__construct()');
 
    	$this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Info());

        $this->initialize();
    }

    public function getValue($key)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('InfoTable->getValue()');
 
        $select = new Select();
        
        $select->from($this->table)
            ->where('infokey="' . $key . '"');
        
        // $firephp->log('InfoTable->getValue(). ' . $select->getSqlString());
        
        $row = $this->selectWith($select)->current();
        
        if ($row == false)
            return "";
        
        return $row->infovalue;
    }
}
