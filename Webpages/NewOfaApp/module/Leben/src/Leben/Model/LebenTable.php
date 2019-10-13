<?php

namespace Leben\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
Use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class LebenTable extends AbstractTableGateway
{
    protected $table = 'ofa_leben';

    public function __construct(Adapter $adapter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenTable->__construct()');
        
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Leben());
        
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
                'datumvon',
                'jahr' => new Expression('YEAR(ofa_leben.datumvon)'),
                'nr',
                'datumbis',
                'ortid',
                'beschreibung',
                'bemerkung'
        ))
            ->join('ofa_ort', 'ofa_leben.ortid = ofa_ort.id', 'ort')
            ->join('ofa_land', 'ofa_ort.landid = ofa_land.id', 'land');
        
        $firephp->log('LebenTable->fetchAll(). SQL: ' . $select->getSqlString());
        
        $paginatorAdapter = new DbSelect($select, $this->adapter, $this->resultSetPrototype);
        
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function fetchAllYears()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenTable->fetchAllYears()');
        
        $select = new Select();
        
        $select->from($this->table)
            ->quantifier('DISTINCT')
            ->columns(array(
                'jahr' => new Expression('YEAR(datumvon)')
        ))
            ->order('jahr DESC');
        
        $firephp->log('LebenTable->fetchAllYears(). ' . $select->getSqlString());
        
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        
        return $resultSet;
    }

    public function getLeben($id)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenTable->getLeben()');
        
        $id = (int) $id;
        $rowset = $this->select(array(
                'id' => $id
        ));
        $row = $rowset->current();
        
        $firephp->log('LebenTable->getLeben(). $id=' . $id . '. $datumvon=' . $row->datumvon . '. $ortid=' . $row->ortid);
        
        if (! $row)
        {
            $firephp->error('LebenTable->getLeben(). Could not find row ' . $id);
            throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function getOrtID($id)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenTable->getOrtID()');
        
        $id = (int) $id;
        $rowset = $this->select(array(
                'id' => $id
        ));
        
        $row = $rowset->current();
        
        if (! $row)
        {
            $firephp->error('LebenTable->getOrtID(). Could not find row ' . $id);
            throw new \Exception("Could not find row $id");
        }
        
        return (int) $row->ortid;
    }

    public function getNextNr($datum)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenTable->countEntriesForDay():' . $datum);
        
        $select = new Select();
        
        $select->from($this->table)
            ->columns(array(
                'maxnr' => new Expression('max(nr)')
        ))
            ->where('datumvon="' . $datum . '"');
        
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        $res = $resultSet->current();
        $firephp->log('LebenTable->countEntriesForDay():' . $res->maxnr);
        
        return $res->maxnr + 1;
    }

    public function saveLeben(Leben $leben)
    {
        $firephp = \FirePHP::getInstance(true);
        
        $data = array(
                'datumvon' => $leben->datumvon,
                'nr' => $this->getNextNr($leben->datumvon),
                'datumbis' => $leben->datumbis,
                'ortid' => $leben->ortid,
                'beschreibung' => $leben->beschreibung,
                'bemerkung' => $leben->bemerkung
        );
        
        $id = (int) $leben->id;
        
        $firephp->log('LebenTable->saveLeben(). ' . $id . "/" . $leben->datumvon . "/" . $leben->nr . "/" . $leben->ortid . "/" . $leben->beschreibung . "/" . $leben->bemerkung);
        
        if ($id == 0)
        {
            $this->insert($data);
            $id = $this->lastInsertValue;
        }
        else
        {
            if ($this->getLeben($id))
            {
                $this->update($data, array(
                        'id' => $id
                ));
            }
            else
            {
                $firephp->error('LebenTable->saveLeben(). Leben id does not exist');
                throw new \Exception('Leben id does not exist');
            }
        }
        
        return $id;
    }

    public function deleteLeben($id)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenTable->deleteLeben()');
        
        $this->delete(array(
                'id' => (int) $id
        ));
    }
}
