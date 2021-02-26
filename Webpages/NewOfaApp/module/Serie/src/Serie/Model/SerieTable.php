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
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Serie($adapter));
        
        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
        if (null === $select)
            $select = new Select();
        
        $select->from($this->table)
            ->columns(array(
                'id',
                'serie',
                'zusatz',
                'labelcheck',
                'extras',
                'link_serie',
                'link_land',
                'link_ort',
                'link_motiv'
        ));
        
        $paginatorAdapter = new DbSelect($select, $this->adapter, $this->resultSetPrototype);
        
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function getSerie($id)
    {
        $id = (int) $id;
        $rowset = $this->select(array(
            'id' => $id
        ));

        $row = $rowset->current();
        
        if (! $row) {
            throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function saveSerie(Serie $serie)
    {
        $id = (int) $serie->id;
        
        $data = array(
            'serie' => $serie->serie,
            'zusatz' => $serie->zusatz,
            'extras' => $serie->extras,
            'link_serie' => $serie->link_serie,
            'link_land' => $serie->link_land,
            'link_ort' => $serie->link_ort,
            'link_motiv' => $serie->link_motiv
        );
        
        if ($id == 0) {
            $this->insert($data);
        }
        else
        {
            if ($this->getSerie($id)) {
                $this->update($data, array(
                    'id' => $id
                ));
            }
            else {
                throw new \Exception('Serie id does not exist');
            }
        }
    }

    public function deleteSerie($id)
    {
        $this->delete(array(
            'id' => (int) $id
        ));
    }
}
