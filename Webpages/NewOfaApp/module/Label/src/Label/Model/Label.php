<?php

namespace Label\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\NoRecordExists;
use Zend\Db\Adapter\Adapter;

class Label implements InputFilterAwareInterface
{
    public $id;
    public $label_en;
    public $label_de;
    public $used;
    protected $inputFilter;
    protected $dbAdapter;

    public function __construct(Adapter $adapter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Label->__construct()');
        
        $this->dbAdapter = $adapter;
    }

    public function exchangeArray($data)
    {
        $firephp = \FirePHP::getInstance(true);
        
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->label_en = (isset($data['label_en'])) ? $data['label_en'] : null;
        $this->label_de = (isset($data['label_de'])) ? $data['label_de'] : null;
        $this->used = (isset($data['used'])) ? $data['used'] : null;
    }

    public function getArrayCopy()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Label->getArrayCopy');
        
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Label->setInputFilter');
        
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Label->getInputFilter: id=' . $this->id);
        
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
                    'name' => 'label_en',
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
                                            'max' => 100
                                    )
                            )
                    )
            ));
            
            $inputFilter->add(array(
                'name' => 'label_de',
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
                                        'max' => 100
                                )
                        )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'used',
                'required' => true,
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
