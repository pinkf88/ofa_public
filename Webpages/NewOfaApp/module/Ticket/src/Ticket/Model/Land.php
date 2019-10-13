<?php
namespace Land\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Land implements InputFilterAwareInterface
{
    public $id;
    public $land;
    public $kurz;
    public $kontinentid;
    public $kontinent;
    protected $inputFilter;

    public function exchangeArray($data)
    {
		$firephp = \FirePHP::getInstance(true);
 
    	$this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->land = (isset($data['land'])) ? $data['land'] : null;
        $this->kurz = (isset($data['kurz'])) ? $data['kurz'] : null;
    	$this->kontinentid = (isset($data['kontinentid'])) ? $data['kontinentid'] : null;
        $this->kontinent = (isset($data['kontinent'])) ? $data['kontinent'] : null;

        $firephp->log('Land->exchangeArray(). ' . $this->id . '/' . $this->land . '/' . $this->kurz . '/' . $this->kontinentid); //  . '/' . $this->kontinent);
    }

    public function getArrayCopy()
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Land->getArrayCopy');
 
    	return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Land->setInputFilter');
 
    	throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Land->getInputFilter');
 
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

            $inputFilter->add(array(
                'name'     => 'kurz',
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
                        'max'      => 3,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
            		'name'     => 'kontinentid',
            		'required' => true,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            ));
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
