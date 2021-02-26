<?php

namespace Track\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
Use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class TrackTable extends AbstractTableGateway
{
    protected $table = 'ofa_tracks';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Track($adapter));

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
        // SELECT id, title, artist, album, discnumber, totaldiscs, track, albumartist, duration, genre, year, musicbrainz_albumid FROM `ofa_tracks` ORDER BY albumartist, album, discnumber, track

        if (null === $select)
            $select = new Select();

        $select->from($this->table)
            ->columns(array(
                'id',
                'title',
                'artist',
                'album',
                'discnumber',
                'totaldiscs',
                'track',
                'albumartist',
                'duration',
                'genre',
                'originalyear',
                'year',
                'musicbrainz_albumid',
                'musicbrainz_trackid',
                'rating',
                'count_play',
                'studio',
                'mean_volume',
                'max_volume'
            ));

        // echo($select->getSqlString());
        $paginatorAdapter = new DbSelect($select, $this->adapter, $this->resultSetPrototype);

        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function fetchAllArtists()
    {
        $select = new Select();

        $select->from($this->table)
            ->quantifier('DISTINCT')
            ->columns(array(
                'albumartist'
            ))
            ->order('albumartist');

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    public function fetchAllAlbums()
    {
        $select = new Select();

        $select->from($this->table)
            ->quantifier('DISTINCT')
            ->columns(array(
                'album', 'musicbrainz_albumid', 'originalyear', 'year'
            ))
            ->order(array(
                'album', 'originalyear', 'year'
            ));

        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        return $resultSet;
    }

    public function getTrack($id)
    {
        $id = (int) $id;
        $rowset = $this->select(array(
                'id' => $id
        ));

        $row = $rowset->current();

        if (! $row)
        {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }
}
