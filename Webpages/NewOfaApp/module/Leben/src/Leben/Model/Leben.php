<?php

namespace Leben\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Leben implements InputFilterAwareInterface
{
    public $id;
    public $datumvon;
    public $jahr;
    public $nr;
    public $maxnr;
    public $datumbis;
    public $ortid;
    public $ort;
    public $landid;
    public $land;
    public $beschreibung;
    public $bemerkung;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $firephp = \FirePHP::getInstance(true);
        
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->datumvon = (isset($data['datumvon'])) ? $data['datumvon'] : null;
        $this->jahr = (isset($data['jahr'])) ? $data['jahr'] : null;
        $this->nr = (isset($data['nr'])) ? $data['nr'] : null;
        $this->maxnr = (isset($data['maxnr'])) ? $data['maxnr'] : null;
        $this->datumbis = (isset($data['datumbis'])) ? $data['datumbis'] : null;
        $this->ortid = (isset($data['ortid'])) ? $data['ortid'] : null;
        $this->ort = (isset($data['ort'])) ? $data['ort'] : null;
        $this->landid = (isset($data['landid'])) ? $data['landid'] : null;
        $this->land = (isset($data['land'])) ? $data['land'] : null;
        $this->beschreibung = (isset($data['beschreibung'])) ? $data['beschreibung'] : null;
        $this->bemerkung = (isset($data['bemerkung'])) ? $data['bemerkung'] : null;
        
        // $firephp->log('Leben->exchangeArray(). ' . $this->id . '/' . $this->leben . '/' . $this->landid . '/' . $this->laenge . '/' . $this->breite); // . '/' . $this->land);
    }

    public function getArrayCopy()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Leben->getArrayCopy');
        
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Leben->setInputFilter');
        
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Leben->getInputFilter');
        
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
            
            $inputFilter->add(array(
                    'name' => 'datumvon',
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
                    'name' => 'nr',
                    'required' => false,
                    'filters' => array(
                            array(
                                    'name' => 'Int'
                            )
                    )
            ));
            
            $inputFilter->add(array(
                    'name' => 'datumbis',
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
                                            'min' => 0,
                                            'max' => 200
                                    )
                            )
                    )
            ));
            
            $inputFilter->add(array(
                    'name' => 'bemerkung',
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
                                            'encoding' => 'UTF-8'
                                    )
                            )
                    )
            ));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}
