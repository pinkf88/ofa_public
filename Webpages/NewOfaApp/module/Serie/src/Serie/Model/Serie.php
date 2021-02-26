<?php

namespace Serie\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\NoRecordExists;
use Zend\Db\Adapter\Adapter;

class Serie implements InputFilterAwareInterface
{
    public $id;
    public $serie;
    public $zusatz;
    public $labelcheck;
    public $extras;
    public $link_serie;
    public $link_land;
    public $link_ort;
    public $link_motiv;
    protected $inputFilter;
    protected $dbAdapter;

    public function __construct(Adapter $adapter)
    {
        $this->dbAdapter = $adapter;
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->serie = (isset($data['serie'])) ? $data['serie'] : null;
        $this->zusatz = (isset($data['zusatz'])) ? $data['zusatz'] : null;
        $this->labelcheck = (isset($data['labelcheck'])) ? $data['labelcheck'] : null;
        $this->extras = (isset($data['extras'])) ? $data['extras'] : null;
        $this->link_serie = (isset($data['link_serie'])) ? $data['link_serie'] : null;
        $this->link_land = (isset($data['link_land'])) ? $data['link_land'] : null;
        $this->link_ort = (isset($data['link_ort'])) ? $data['link_ort'] : null;
        $this->link_motiv = (isset($data['link_motiv'])) ? $data['link_motiv'] : null;
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
            
            $validatorSerie = null;
            
            if ($this->id > 0)
            {
                $validatorSerie = new NoRecordExists(array(
                    'adapter' => $this->dbAdapter,
                    'table' => 'ofa_serie',
                    'field' => 'serie',
                    'exclude' => array(
                        'field' => 'id',
                        'value' => $this->id
                )
                ));
            }
            else
            {
                $validatorSerie = new NoRecordExists(array(
                    'adapter' => $this->dbAdapter,
                    'table' => 'ofa_serie',
                    'field' => 'serie'
                ));
            }
            
            $inputFilter->add(array(
                'name' => 'serie',
                'required' => false,
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
                                'max' => 120
                        )
                    ),
                    $validatorSerie
                )
            ));

            $inputFilter->add(array(
                'name'     => 'zusatz',
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
                'name'     => 'extras',
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
                'name' => 'link_serie',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'link_land',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'link_ort',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'link_motiv',
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
