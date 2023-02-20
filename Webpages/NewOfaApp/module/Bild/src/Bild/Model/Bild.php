<?php

namespace Bild\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\NoRecordExists;
use Zend\Db\Adapter\Adapter;

class Bild implements InputFilterAwareInterface
{
    public $id;
    public $nummer;
    public $datei;
    public $datum;
    public $jahr;
    public $jahrflag;
    public $ortid;
    public $ort;
    public $landid;
    public $land;
    public $beschreibung;
    public $bemerkung;
    public $wertung;
    public $panorama;
    public $ticket;
    public $ohneort;
    public $ohneland;
    public $polygon;
    public $anzahl;
    protected $inputFilter;
    protected $dbAdapter;

    public function __construct(Adapter $adapter)
    {
        $this->dbAdapter = $adapter;
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->nummer = (isset($data['nummer'])) ? $data['nummer'] : null;
        $this->datei = (isset($data['datei'])) ? $data['datei'] : null;
        $this->datum = (isset($data['datum'])) ? $data['datum'] : null;
        $this->jahr = (isset($data['jahr'])) ? $data['jahr'] : null;
        $this->jahrflag = (isset($data['jahrflag'])) ? $data['jahrflag'] : null;
        $this->ortid = (isset($data['ortid'])) ? $data['ortid'] : null;
        $this->ort = (isset($data['ort'])) ? $data['ort'] : null;
        $this->landid = (isset($data['landid'])) ? $data['landid'] : null;
        $this->land = (isset($data['land'])) ? $data['land'] : null;
        $this->beschreibung = (isset($data['beschreibung'])) ? $data['beschreibung'] : null;
        $this->bemerkung = (isset($data['bemerkung'])) ? $data['bemerkung'] : null;
        $this->wertung = (isset($data['wertung'])) ? $data['wertung'] : null;
        $this->panorama = (isset($data['panorama'])) ? $data['panorama'] : null;
        $this->ticket = (isset($data['ticket'])) ? $data['ticket'] : null;
        $this->ohneort = (isset($data['ohneort'])) ? $data['ohneort'] : null;
        $this->ohneland = (isset($data['ohneland'])) ? $data['ohneland'] : null;
        $this->polygon = (isset($data['polygon'])) ? $data['polygon'] : null;
        $this->anzahl = (isset($data['anzahl'])) ? $data['anzahl'] : null;
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
        if (!$this->inputFilter) {
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

            $validatorNummer = null;

            if ($this->id > 0) {
                $validatorNummer = new NoRecordExists(array(
                    'adapter' => $this->dbAdapter,
                    'table' => 'ofa_bild',
                    'field' => 'nummer',
                    'exclude' => array(
                        'field' => 'id',
                        'value' => $this->id
                    )
                ));
            } else {
                $validatorNummer = new NoRecordExists(array(
                    'adapter' => $this->dbAdapter,
                    'table' => 'ofa_bild',
                    'field' => 'nummer'
                ));
            }

            $inputFilter->add(array(
                'name' => 'nummer',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                ),
                'validators' => array(
                    $validatorNummer
                )
            ));

            $validatorDatei = null;

            if ($this->id > 0) {
                $validatorDatei = new NoRecordExists(array(
                    'adapter' => $this->dbAdapter,
                    'table' => 'ofa_bild',
                    'field' => 'datei',
                    'exclude' => array(
                        'field' => 'id',
                        'value' => $this->id
                    )
                ));
            } else {
                $validatorDatei = new NoRecordExists(array(
                    'adapter' => $this->dbAdapter,
                    'table' => 'ofa_bild',
                    'field' => 'datei'
                ));
            }

            $inputFilter->add(array(
                'name' => 'datei',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                ),
                'validators' => array(
                    $validatorDatei
                )
            ));

            $inputFilter->add(array(
                'name' => 'datum',
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
                'name' => 'jahrflag',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'ortid',
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
                'name' => 'beschreibung',
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 200
                        )
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'bemerkung',
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8'
                        )
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'wertung',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'panorama',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'ticket',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'ohneort',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'ohneland',
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
