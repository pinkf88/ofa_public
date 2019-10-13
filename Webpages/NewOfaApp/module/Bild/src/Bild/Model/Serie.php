<?php
namespace Bild\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Serie implements InputFilterAwareInterface
{
    public $id;
    public $serie;
    protected $inputFilter;

    public function exchangeArray($data)
    {
		// $firephp = \FirePHP::getInstance(true);
 
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->serie = (isset($data['serie'])) ? $data['serie'] : null;
        
//         $firephp->log('Serie->exchangeArray(). ' . $this->id . '/' . $this->serie);
    }

    public function getArrayCopy()
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('Serie->getArrayCopy()');
 
    	return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('Serie->setInputFilter()');
 
    	throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('Serie->getInputFilter()');
 
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
                'name'     => 'serie',
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
                        'max'      => 120,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}

