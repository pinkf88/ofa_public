<?php
namespace Web\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Land
{
    public $id;
    public $land;
    public $kurz;
    public $kontinentid;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->land = (isset($data['land'])) ? $data['land'] : null;
        $this->kurz = (isset($data['kurz'])) ? $data['kurz'] : null;
        $this->kontinentid = (isset($data['kontinentid'])) ? $data['kontinentid'] : null;
        
		$firephp = \FirePHP::getInstance(true);
        $firephp->log('Land->exchangeArray(). ' . $this->id . '/' . $this->land . '/' . $this->kontinentid);
    }

    public function getArrayCopy()
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Land->getArrayCopy()');
 
    	return get_object_vars($this);
    }
}

