<?php
namespace Bild\Model;

class Info
{
    public $id;
    public $infokey;
    public $infovalue;

    public function exchangeArray($data)
    {
		// $firephp = \FirePHP::getInstance(true);
 
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->infokey = (isset($data['infokey'])) ? $data['infokey'] : null;
        $this->infovalue = (isset($data['infovalue'])) ? $data['infovalue'] : null;
        
//         $firephp->log('Info->exchangeArray(). ' . $this->id . '/' . $this->land);
    }

    public function getArrayCopy()
    {
		// $firephp = \FirePHP::getInstance(true);
		// $firephp->log('Info->getArrayCopy()');
 
    	return get_object_vars($this);
    }
}
