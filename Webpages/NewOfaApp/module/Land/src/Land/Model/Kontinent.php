<?php
namespace Land\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Kontinent implements InputFilterAwareInterface
{
    public $id;
    public $kontinent;
    protected $inputFilter;

    public function exchangeArray($data)
    {
		$firephp = \FirePHP::getInstance(true);
 
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->kontinent = (isset($data['kontinent'])) ? $data['kontinent'] : null;
        
        $firephp->log('Kontinent->exchangeArray(). ' . $this->id . '/' . $this->kontinent);
    }

    public function getArrayCopy()
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Kontinent->getArrayCopy()');
 
    	return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Kontinent->setInputFilter()');
 
    	throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Kontinent->getInputFilter()');
 
    	if (!$this->inputFilter)
        {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'kontinent',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 20,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}

