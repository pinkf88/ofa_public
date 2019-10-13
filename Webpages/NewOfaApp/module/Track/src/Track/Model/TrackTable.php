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
                'year',
                'musicbrainz_albumid',
                'musicbrainz_trackid'
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
                'album', 'musicbrainz_albumid', 'year'
            ))
            ->order('album');

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
            // $firephp->error('TrackTable->getTrack(). Could not find row ' . $id);
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function saveTrack(Track $track)
    {
        $data = array(
                'artist' => $track->artist,
                'album' => $track->album,
        );

        $musicbrainz_albumid = (int) $track->musicbrainz_albumid;

        // $firephp->log('TrackTable->saveTrack(). ' . $musicbrainz_albumid . "/" . $track->nummer . "/" . $track->datei . "/" . $track->datum);

        if ($musicbrainz_albumid == 0)
        {
            /*
            $this->insert($data);
            $musicbrainz_albumid = $this->lastInsertValue;
            */
        }
        else
        {
            if ($this->getTrack($id))
            {
                $this->update($data, array(
                        'musicbrainz_albumid' => $musicbrainz_albumid
                ));
            }
            else
            {
                // $firephp->error('TrackTable->saveTrack(). Track musicbrainz_albumid does not exist');
                throw new \Exception('Track musicbrainz_albumid does not exist');
            }
        }

        return $musicbrainz_albumid;
    }

    public function deleteTrack($id)
    {
        $this->delete(array(
                'id' => (int) $id
        ));
    }
}
