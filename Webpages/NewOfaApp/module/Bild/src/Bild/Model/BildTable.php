<?php
namespace Bild\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
Use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class BildTable extends AbstractTableGateway
{
    protected $table = 'ofa_bild';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Bild($adapter));

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
        if (null === $select) {
            $select = new Select();
        }

        $select->from($this->table)
            ->columns(array(
                'id',
                'nummer',
                'datei',
                'datum',
                'jahr' => new Expression('YEAR(ofa_bild.datum)'),
                'jahrflag',
                'ortid',
                'beschreibung',
                'bemerkung',
                'wertung',
                'panorama',
                'ticket',
                'ohneort',
                'ohneland',
                'anzahl' => new Expression('COUNT(ofa_bild_motiv.bildid)')
            ))
            ->join('ofa_ort', 'ofa_bild.ortid = ofa_ort.id', 'ort')
            ->join('ofa_land', 'ofa_ort.landid = ofa_land.id', 'land')
            ->join('ofa_bilddaten', 'ofa_bilddaten.BildNr = ofa_bild.datei', 'polygon', $select::JOIN_LEFT)
            ->join('ofa_bild_motiv', 'ofa_bild_motiv.bildid = ofa_bild.id', array(), $select::JOIN_LEFT)
            ->group(array('ofa_bild.id'));

        // echo 'BildTable->fetchAll(): ' . $select->getSqlString();
        $paginatorAdapter = new DbSelect($select, $this->adapter, $this->resultSetPrototype);

        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function fetchAllYears()
    {
        $select = new Select();

        $select->from($this->table)
            ->quantifier('DISTINCT')
            ->columns(array(
                'jahr' => new Expression('YEAR(datum)')
            ))
            ->order('jahr DESC');

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    public function getBild($id)
    {
        $id = (int) $id;

        $rowset = $this->select(array(
            'id' => $id
        ));

        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function getOrtID($id)
    {
        $id = (int) $id;

        $rowset = $this->select(array(
            'id' => $id
        ));

        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return (int) $row->ortid;
    }

    public function saveBild(Bild $bild)
    {
        if ($bild->datei == '0') {
            $bild->datei = '';
        }

        $data = array(
            'nummer' => $bild->nummer,
            'datei' => $bild->datei,
            'datum' => $bild->datum,
            'jahrflag' => $bild->jahrflag,
            'ortid' => $bild->ortid,
            'beschreibung' => $bild->beschreibung,
            'bemerkung' => $bild->bemerkung,
            'wertung' => $bild->wertung,
            'panorama' => $bild->panorama,
            'ticket' => $bild->ticket,
            'ohneort' => $bild->ohneort,
            'ohneland' => $bild->ohneland,
        );

        $id = (int) $bild->id;

        if ($id == 0) {
            $this->insert($data);
            $id = $this->lastInsertValue;
        } else {
            if ($this->getBild($id)) {
                $this->update($data, array(
                        'id' => $id
                ));
            } else {
                throw new \Exception('Bild id does not exist');
            }
        }

        return $id;
    }

    public function deleteBild($id)
    {
        $this->delete(array(
            'id' => (int) $id
        ));
    }
}
