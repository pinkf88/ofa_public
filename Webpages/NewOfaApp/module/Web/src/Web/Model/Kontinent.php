<?php
namespace Web\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Kontinent
{
    public $id;
    public $kontinent;

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
}

