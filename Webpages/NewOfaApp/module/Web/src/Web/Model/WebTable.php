<?php

namespace Web\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
Use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class WebTable extends AbstractTableGateway
{
    protected $table = 'ofa_web';

    public function __construct(Adapter $adapter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebTable->__construct()');
        
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Web());
        
        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
        $firephp = \FirePHP::getInstance(true);
        
        if (null === $select)
            $select = new Select();
        
        $select->from($this->table)
            ->columns(array(
                'id',
                'web',
                'pfad',
                'zusatz1',
                'zusatz2',
                'nummer'
        ));
        
        $firephp->log('WebTable->fetchAll(). SQL: ' . $select->getSqlString());
        
        $paginatorAdapter = new DbSelect($select, $this->adapter, $this->resultSetPrototype);
        
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function getWeb($id)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebTable->getWeb()');
        
        $id = (int) $id;
        $rowset = $this->select(array(
                'id' => $id
        ));
        $row = $rowset->current();
        
        $firephp->log('WebTable->getWeb(). $id=' . $id . '. $web=' . $row->web);
        
        if (! $row)
        {
            $firephp->error('WebTable->getWeb(). Could not find row ' . $id);
            throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function saveWeb(Web $web)
    {
        $firephp = \FirePHP::getInstance(true);
        
        $id = (int) $web->id;
        
        $data = array(
                'web' => $web->web,
                'pfad' => $web->pfad,
                'zusatz1' => $web->zusatz1,
                'zusatz2' => $web->zusatz2,
                'nummer' => $web->nummer
        );
        
        $firephp->log('WebTable->saveWeb(). ' . $id . "/" . $web->web . "/" . $web->pfad . "/" . $web->zusatz1 . "/" . $web->zusatz2);
        
        if ($id == 0)
        {
            $this->insert($data);
        }
        else
        {
            if ($this->getWeb($id))
            {
                $this->update($data, array(
                        'id' => $id
                ));
            }
            else
            {
                $firephp->error('WebTable->saveWeb(). Web id does not exist');
                throw new \Exception('Web id does not exist');
            }
        }
    }

    public function deleteWeb($id)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebTable->deleteWeb()');
        
        $this->delete(array(
                'id' => (int) $id
        ));
    }
}
