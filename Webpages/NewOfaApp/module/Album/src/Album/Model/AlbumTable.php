<?php

namespace Album\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
Use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class AlbumTable extends AbstractTableGateway
{
    protected $table = 'ofa_tracks';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Album($adapter));

        $this->initialize();
    }

    public function fetchAll(Select $select = null)
    {
        // SELECT musicbrainz_albumid, albumartist, album, year, originalyear, COUNT(musicbrainz_recordingid) FROM `ofa_tracks` GROUP BY musicbrainz_albumid, albumartist, album, year, originalyear ORDER BY albumartist, album

        if (null === $select)
            $select = new Select();

        $select->from($this->table)
            ->columns(array(
                'musicbrainz_albumid',
                'albumartist',
                'albumartistsort',
                'album',
                'year',
                'originalyear',
                'anzahl' => new Expression('COUNT(musicbrainz_recordingid)'),
                'ownerid'
            ))
            ->group(array('musicbrainz_albumid', 'albumartist', 'albumartistsort', 'album', 'year', 'originalyear'));

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

    public function getAlbum($musicbrainz_albumid)
    {
        $rowset = $this->select(array(
                'musicbrainz_albumid' => $musicbrainz_albumid
        ));

        $row = $rowset->current();

        if (! $row)
        {
            throw new \Exception("Could not find row $musicbrainz_albumid");
        }

        return $row;
    }

    public function saveAlbum(Album $album)
    {
        $data = array(
                'musicbrainz_albumid' => $album->musicbrainz_albumid,
                'albumartist' => $album->albumartist,
                'album' => $album->album,
                'year' => $album->year,
                'originalyear' => $album->originalyear,
                'ownerid' => $album->ownerid
        );

        if ($musicbrainz_albumid == 0)
        {
            $this->insert($data);
            $musicbrainz_albumid = $this->lastInsertValue;
        }
        else
        {
            if ($this->getAlbum($musicbrainz_albumid))
            {
                $this->update($data, array(
                    'musicbrainz_albumid' => $musicbrainz_albumid
                ));
            }
            else
            {
                // $firephp->error('AlbumTable->saveAlbum(). Album musicbrainz_albumid does not exist');
                throw new \Exception('Album musicbrainz_albumid does not exist');
            }
        }

        return $musicbrainz_albumid;
    }

    public function deleteAlbum($musicbrainz_albumid)
    {
        $this->delete(array(
            'musicbrainz_albumid' => $musicbrainz_albumid
        ));
    }
}
