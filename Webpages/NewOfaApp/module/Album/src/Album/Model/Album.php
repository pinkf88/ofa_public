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

            $inputFilter->add(array(
                'name' => 'ownerid',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
