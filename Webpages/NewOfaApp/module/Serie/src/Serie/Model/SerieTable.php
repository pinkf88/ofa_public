<?php

namespace Serie\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
Use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SerieTable extends AbstractTableGateway
{
    protected $table = 'ofa_serie';

    public function __construct(Adapter $adapter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('SerieTable->__construct()');
        
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Serie($adapter));
        
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
                'serie',
                'zusatz',
                'labelcheck'
        ));
        
        $firephp->log('SerieTable->fetchAll(). SQL: ' . $select->getSqlString());
        
        $paginatorAdapter = new DbSelect($select, $this->adapter, $this->resultSetPrototype);
        
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function getSerie($id)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('SerieTable->getSerie()');
        
        $id = (int) $id;
        $rowset = $this->select(array(
                'id' => $id
        ));
        $row = $rowset->current();
        
        $firephp->log('SerieTable->getSerie(). $id=' . $id . '. $serie=' . $row->serie);
        
        if (! $row)
        {
            $firephp->error('SerieTable->getSerie(). Could not find row ' . $id);
            throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function saveSerie(Serie $serie)
    {
        $firephp = \FirePHP::getInstance(true);
        
        $id = (int) $serie->id;
        
        $firephp->log('SerieTable->saveSerie(). ' . $id . "/" . $serie->serie);
        
        $data = array(
                'serie' => $serie->serie,
                'zusatz' => $serie->zusatz
        );
        
        if ($id == 0)
        {
            $this->insert($data);
        }
        else
        {
            if ($this->getSerie($id))
            {
                $this->update($data, array(
                        'id' => $id
                ));
            }
            else
            {
                $firephp->error('SerieTable->saveSerie(). Serie id does not exist');
                throw new \Exception('Serie id does not exist');
            }
        }
    }

    public function deleteSerie($id)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('SerieTable->deleteSerie()');
        
        $this->delete(array(
                'id' => (int) $id
        ));
    }
}
