<?php
namespace Bild\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class BildMotiv implements InputFilterAwareInterface
{
    public $id;
    public $motivid;
    protected $inputFilter;

    public function exchangeArray($data)
    {
		// $firephp = \FirePHP::getInstance(true);
 
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->motivid = (isset($data['motivid'])) ? $data['motivid'] : null;
        
//         $firephp->log('BildMotiv->exchangeArray(). ' . $this->id . '/' . $this->motivid);
    }

    public function getArrayCopy()
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('BildMotiv->getArrayCopy()');
 
    	return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('BildMotiv->setInputFilter()');
 
    	throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('BildMotiv->getInputFilter()');
 
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
                'name'     => 'motivid',
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

