<?php

namespace Web\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Web implements InputFilterAwareInterface
{
    public $id;
    public $web;
    public $landid;
    public $pfad;
    public $zusatz1;
    public $zusatz2;
    public $nummer;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $firephp = \FirePHP::getInstance(true);
        
        $firephp->log('Web->exchangeArray(). pfad=' . $data['pfad']);
        
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->landid = (isset($data['landid'])) ? $data['landid'] : null;
        $this->web = (isset($data['web'])) ? $data['web'] : null;
        $this->pfad = (isset($data['pfad'])) ? $data['pfad'] : null;
        $this->zusatz1 = (isset($data['zusatz1'])) ? $data['zusatz1'] : null;
        $this->zusatz2 = (isset($data['zusatz2'])) ? $data['zusatz2'] : null;
        $this->nummer = (isset($data['nummer'])) ? $data['nummer'] : null;
        
        $firephp->log('Web->exchangeArray(). ' . $this->id . '/' . $this->landid . '/' . $this->web . '/' . $this->pfad . '/' . $this->zusatz1 . '/' . $this->zusatz2);
    }

    public function getArrayCopy()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Web->getArrayCopy');
        
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Web->setInputFilter');
        
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('Web->getInputFilter');
        
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
                'name' => 'landid',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'web',
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
                                'max' => 200
                        )
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'pfad',
                'required' => false,
                'filters' => array(
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
                'name' => 'zusatz1',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 'zusatz2',
                'required' => false,
            ));

            $inputFilter->add(array(
                    'name' => 'nummer',
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
