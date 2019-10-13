<?php

namespace Ort\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\NoRecordExists;
use Zend\Db\Adapter\Adapter;

class Ort implements InputFilterAwareInterface
{
    public $id;
    public $ort;
    public $landid;
    public $land;
    public $laenge;
    public $breite;
    protected $inputFilter;
    protected $dbAdapter;

    public function __construct(Adapter $adapter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Ort->__construct()');
        
        $this->dbAdapter = $adapter;
    }

    public function exchangeArray($data)
    {
        $firephp = \FirePHP::getInstance(true);
        
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->ort = (isset($data['ort'])) ? $data['ort'] : null;
        $this->landid = (isset($data['landid'])) ? $data['landid'] : null;
        $this->land = (isset($data['land'])) ? $data['land'] : null;
        $this->laenge = (isset($data['laenge'])) ? $data['laenge'] : null;
        $this->breite = (isset($data['breite'])) ? $data['breite'] : null;
        
        // $firephp->log('Ort->exchangeArray(). ' . $this->id . '/' . $this->ort . '/' . $this->landid . '/' . $this->laenge . '/' . $this->breite); // . '/' . $this->land);
    }

    public function getArrayCopy()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Ort->getArrayCopy');
        
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Ort->setInputFilter');
        
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Ort->getInputFilter');
        
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
            
            $validatorOrt = null;
            
            if ($this->id > 0)
            {
                $validatorOrt = new NoRecordExists(array(
                        'adapter' => $this->dbAdapter,
                        'table' => 'ofa_ort',
                        'field' => 'ort',
                        'exclude' => array(
                                'field' => 'id',
                                'value' => $this->id
                        )
                ));
            }
            else
            {
                $validatorOrt = new NoRecordExists(array(
                        'adapter' => $this->dbAdapter,
                        'table' => 'ofa_ort',
                        'field' => 'ort'
                ));
            }
            
            $inputFilter->add(array(
                    'name' => 'ort',
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
                                            'max' => 50
                                    )
                            ),
                            $validatorOrt
                    )
            ));
            
            $inputFilter->add(array(
                    'name' => 'landid',
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
                    'name' => 'laenge',
                    'required' => false,
                    'filters' => array(
                            array(
                                    'name' => 'Int'
                            )
                    )
            ));
            
            $inputFilter->add(array(
                    'name' => 'breite',
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
