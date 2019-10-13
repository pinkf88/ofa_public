<?php
namespace Bild\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Motiv implements InputFilterAwareInterface
{
    public $id;
    public $motiv;
    protected $inputFilter;

    public function exchangeArray($data)
    {
		// $firephp = \FirePHP::getInstance(true);
 
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->motiv = (isset($data['motiv'])) ? $data['motiv'] : null;
        
//         $firephp->log('Motiv->exchangeArray(). ' . $this->id . '/' . $this->motiv);
    }

    public function getArrayCopy()
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('Motiv->getArrayCopy()');
 
    	return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('Motiv->setInputFilter()');
 
    	throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('Motiv->getInputFilter()');
 
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
                'name'     => 'motiv',
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
                        'max'      => 50,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}

