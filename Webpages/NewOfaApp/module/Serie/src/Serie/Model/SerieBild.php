<?php

namespace Serie\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class SerieBild implements InputFilterAwareInterface
{
    public $id;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->serieid = (isset($data['serieid'])) ? $data['serieid'] : null;
        $this->nr = (isset($data['nr'])) ? $data['nr'] : null;
        $this->bildid = (isset($data['bildid'])) ? $data['bildid'] : null;
        
        // $firephp = \FirePHP::getInstance(true);
        // $firephp->log('SerieBild->exchangeArray(). ' . $this->id . '/' . $this->ort);
    }

    public function getArrayCopy()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('SerieBild->getArrayCopy()');
        
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('SerieBild->setInputFilter()');
        
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('SerieBild->getInputFilter()');
        
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
                'name' => 'serieid',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'nr',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'bildid',
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
