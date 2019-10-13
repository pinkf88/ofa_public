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
    protected $inputFilter;
    protected $dbAdapter;

    public function __construct(Adapter $adapter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Serie->__construct()');
        
        $this->dbAdapter = $adapter;
    }

    public function exchangeArray($data)
    {
        $firephp = \FirePHP::getInstance(true);
        
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->serie = (isset($data['serie'])) ? $data['serie'] : null;
        $this->zusatz = (isset($data['zusatz'])) ? $data['zusatz'] : null;
        $this->labelcheck = (isset($data['labelcheck'])) ? $data['labelcheck'] : null;
        
        // $firephp->log('Serie->exchangeArray(). ' . $this->id . '/' . $this->serie . '/' . $this->landid . '/' . $this->laenge . '/' . $this->breite); // . '/' . $this->land);
    }

    public function getArrayCopy()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Serie->getArrayCopy');
        
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Serie->setInputFilter');
        
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Serie->getInputFilter');
        
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
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}
