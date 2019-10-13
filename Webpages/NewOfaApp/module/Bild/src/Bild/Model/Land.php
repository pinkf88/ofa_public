<?php
namespace Bild\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Land implements InputFilterAwareInterface
{
    public $id;
    public $land;
    protected $inputFilter;

    public function exchangeArray($data)
    {
		// $firephp = \FirePHP::getInstance(true);
 
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->land = (isset($data['land'])) ? $data['land'] : null;
        
//         $firephp->log('Land->exchangeArray(). ' . $this->id . '/' . $this->land);
    }

    public function getArrayCopy()
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('Land->getArrayCopy()');
 
    	return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('Land->setInputFilter()');
 
    	throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('Land->getInputFilter()');
 
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
                'name'     => 'land',
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
                        'max'      => 30,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}

