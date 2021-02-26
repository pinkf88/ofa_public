<?php

namespace Track\Model;

// use Zend\InputFilter\InputFilter;
//  use Zend\InputFilter\InputFilterAwareInterface;
// use Zend\InputFilter\InputFilterInterface;
// use Zend\Validator\Db\NoRecordExists;
use Zend\Db\Adapter\Adapter;

class Track // implements InputFilterAwareInterface
{
    // SELECT id, title, artist, album, discnumber, totaldiscs, track, albumartist, duration, genre, musicbrainz_albumid FROM `ofa_tracks` ORDER BY albumartist, album, discnumber, track

    public $id;
    public $title;
    public $artist;
    public $albumartist;
    public $album;
    public $discnumber;
    public $totaldiscs;
    public $track;
    public $duration;
    public $genre;
    public $count_play;
    public $studio;
    public $originalyear;
    public $year;
    public $musicbrainz_albumid;
    public $musicbrainz_trackid;
    public $mean_volume;
    public $max_volume;
    // protected $inputFilter;
    protected $dbAdapter;

    public function __construct(Adapter $adapter)
    {
        $this->dbAdapter = $adapter;
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->title = (isset($data['title'])) ? $data['title'] : null;
        $this->artist = (isset($data['artist'])) ? $data['artist'] : null;
        $this->albumartist = (isset($data['albumartist'])) ? $data['albumartist'] : null;
        $this->album = (isset($data['album'])) ? $data['album'] : null;
        $this->discnumber = (isset($data['discnumber'])) ? $data['discnumber'] : null;
        $this->totaldiscs = (isset($data['totaldiscs'])) ? $data['totaldiscs'] : null;
        $this->track = (isset($data['track'])) ? $data['track'] : null;
        $this->duration = (isset($data['duration'])) ? $data['duration'] : null;
        $this->genre = (isset($data['genre'])) ? $data['genre'] : null;
        $this->count_play = (isset($data['count_play'])) ? $data['count_play'] : null;
        $this->studio = (isset($data['studio'])) ? $data['studio'] : null;
        $this->originalyear = (isset($data['originalyear'])) ? $data['originalyear'] : null;
        $this->year = (isset($data['year'])) ? $data['year'] : null;
        $this->musicbrainz_albumid = (isset($data['musicbrainz_albumid'])) ? $data['musicbrainz_albumid'] : null;
        $this->musicbrainz_trackid = (isset($data['musicbrainz_trackid'])) ? $data['musicbrainz_trackid'] : null;
        $this->mean_volume = (isset($data['mean_volume'])) ? $data['mean_volume'] : null;
        $this->max_volume = (isset($data['max_volume'])) ? $data['max_volume'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
