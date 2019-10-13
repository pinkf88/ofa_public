<?php

namespace Track\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\NoRecordExists;
use Zend\Db\Adapter\Adapter;

class Track implements InputFilterAwareInterface
{
    // SELECT id, title, artist, album, discnumber, totaldiscs, track, albumartist, duration, genre, musicbrainz_albumid FROM `ofa_tracks` ORDER BY albumartist, album, discnumber, track

    public $id;
    public $title;
    public $albumartist;
    public $album;
    public $musicbrainz_albumid;
    protected $inputFilter;
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
        $this->year = (isset($data['year'])) ? $data['year'] : null;
        $this->musicbrainz_albumid = (isset($data['musicbrainz_albumid'])) ? $data['musicbrainz_albumid'] : null;
        $this->musicbrainz_trackid = (isset($data['musicbrainz_trackid'])) ? $data['musicbrainz_trackid'] : null;
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
        if (! $this->inputFilter)
        {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                            array(
                                    'name' => 'Int'
                            )
                    )
            ));

            $validatormusicbrainz_albumid = null;

            if ($this->id > 0)
            {
                $validatormusicbrainz_albumid = new NoRecordExists(array(
                        'adapter' => $this->dbAdapter,
                        'table' => 'ofa_tracks',
                        'field' => 'musicbrainz_albumid',
                        'exclude' => array(
                                'field' => 'id',
                                'value' => $this->id
                        )
                ));
            }
            else
            {
                $validatormusicbrainz_albumid = new NoRecordExists(array(
                        'adapter' => $this->dbAdapter,
                        'table' => 'ofa_tracks',
                        'field' => 'musicbrainz_albumid'
                ));
            }

            $inputFilter->add(array(
                    'name' => 'musicbrainz_albumid',
                    'required' => true,
                    'filters' => array(
                            array(
                                    'name' => 'Int'
                            )
                    ),
                    'validators' => array(
                            $validatormusicbrainz_albumid
                    )
            ));

            $validatoralbumartist = null;

            if ($this->id > 0)
            {
                $validatoralbumartist = new NoRecordExists(array(
                        'adapter' => $this->dbAdapter,
                        'table' => 'ofa_tracks',
                        'field' => 'albumartist',
                        'exclude' => array(
                                'field' => 'id',
                                'value' => $this->id
                        )
                ));
            }
            else
            {
                $validatoralbumartist = new NoRecordExists(array(
                        'adapter' => $this->dbAdapter,
                        'table' => 'ofa_tracks',
                        'field' => 'albumartist'
                ));
            }

            $inputFilter->add(array(
                    'name' => 'albumartist',
                    'required' => false,
                    'filters' => array(
                            array(
                                    'name' => 'Int'
                            )
                    ),
                    'validators' => array(
                            $validatoralbumartist
                    )
            ));

            $inputFilter->add(array(
                    'name' => 'album',
                    'required' => true,
                    'filters' => array(
                            array(
                                    'name' => 'StripTags'
                            ),
                            array(
                                    'name' => 'StringTrim'
                            )
                    ),
                    'validators' => array(
                            array(
                                    'name' => 'StringLength',
                                    'options' => array(
                                            'encoding' => 'UTF-8',
                                            'min' => 1,
                                            'max' => 10
                                    )
                            )
                    )
            ));

            $inputFilter->add(array(
                    'name' => 'originalyear',
                    'required' => false,
                    'filters' => array(
                            array(
                                    'name' => 'Int'
                            )
                    )
            ));

            $inputFilter->add(array(
                    'name' => 'anzahl',
                    'required' => true,
                    'filters' => array(
                            array(
                                    'name' => 'Int'
                            )
                    ),
                    'validators' => array(
                            array(
                                    'name' => 'GreaterThan',
                                    'options' => array(
                                            'min' => 0
                                    )
                            )
                    )
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
