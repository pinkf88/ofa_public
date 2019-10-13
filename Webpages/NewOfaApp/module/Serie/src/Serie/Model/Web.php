<?php
namespace Serie\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Web
{
    public $id;
    public $web;

    public function exchangeArray($data)
    {
		$firephp = \FirePHP::getInstance(true);
 
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->web = (isset($data['web'])) ? $data['web'] : null;
        
//         $firephp->log('Web->exchangeArray(). ' . $this->id . '/' . $this->web);
    }

    public function getArrayCopy()
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Web->getArrayCopy()');
 
    	return get_object_vars($this);
    }
}

