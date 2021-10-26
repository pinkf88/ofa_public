<?php

namespace Album\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\NoRecordExists;
use Zend\Db\Adapter\Adapter;

class Album implements InputFilterAwareInterface
{
    // SELECT musicbrainz_albumid, albumartist, album, year, originalyear, COUNT(musicbrainz_recordingid) FROM `ofa_tracks` GROUP BY musicbrainz_albumid, albumartist, album, year, originalyear ORDER BY albumartist, album

    public $id;
    public $musicbrainz_albumid;
    public $albumartist;
    public $album;
    public $year;
    public $originalyear;
    public $anzahl;
    public $ownerid;
    public $rating;
    public $genre;
    public $studio;
    public $compilation;
    protected $inputFilter;
    protected $dbAdapter;

    public function __construct(Adapter $adapter)
    {
        $this->dbAdapter = $adapter;
    }

    public function exchangeArray($data)
    {
        $this->musicbrainz_albumid = (isset($data['musicbrainz_albumid'])) ? $data['musicbrainz_albumid'] : null;
        $this->albumartist = (isset($data['albumartist'])) ? $data['albumartist'] : null;
        $this->album = (isset($data['album'])) ? $data['album'] : null;
        $this->year = (isset($data['year'])) ? $data['year'] : null;
        $this->originalyear = (isset($data['originalyear'])) ? $data['originalyear'] : null;
        $this->anzahl = (isset($data['anzahl'])) ? $data['anzahl'] : null;
        $this->ownerid = (isset($data['ownerid'])) ? $data['ownerid'] : null;
        $this->rating = (isset($data['rating'])) ? $data['rating'] : null;
        $this->genre = (isset($data['genre'])) ? $data['genre'] : null;
        $this->studio = (isset($data['studio'])) ? $data['studio'] : null;
        $this->compilation = (isset($data['compilation'])) ? $data['compilation'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
    }
}
