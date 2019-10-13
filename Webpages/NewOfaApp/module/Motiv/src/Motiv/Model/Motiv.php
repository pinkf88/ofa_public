<?php
namespace Motiv\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Motiv implements InputFilterAwareInterface
{
    public $id;
    public $motiv;
    public $laenge;
    public $breite;
    public $link;
    public $mapzoom;
    public $ortid;
    public $ort;
    public $ortlaenge;
    public $ortbreite;
    protected $inputFilter;

    public function exchangeArray($data)
    {
		$firephp = \FirePHP::getInstance(true);
 
    	$this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->motiv = (isset($data['motiv'])) ? $data['motiv'] : null;
        $this->laenge = (isset($data['laenge'])) ? $data['laenge'] : null;
        $this->breite = (isset($data['breite'])) ? $data['breite'] : null;
        $this->link = (isset($data['link'])) ? $data['link'] : null;
        $this->mapzoom = (isset($data['mapzoom'])) ? $data['mapzoom'] : null;
        $this->ortid = (isset($data['ortid'])) ? $data['ortid'] : null;
        $this->ort = (isset($data['ort'])) ? $data['ort'] : null;
        $this->ortlaenge = (isset($data['ortlaenge'])) ? $data['ortlaenge'] : null;
        $this->ortbreite = (isset($data['ortbreite'])) ? $data['ortbreite'] : null;
        
//         $firephp->log('Motiv->exchangeArray(). ' . $this->id . '/' . $this->motiv . '/' . $this->ortid . '/' . $this->laenge . '/' . $this->breite); //  . '/' . $this->ort);
    }

    public function getArrayCopy()
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Motiv->getArrayCopy');
 
    	return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Motiv->setInputFilter');
 
    	throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
		$firephp = \FirePHP::getInstance(true);
		$firephp->log('Motiv->getInputFilter');
 
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
                        'max'      => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
            		'name'     => 'ortid',
            		'required' => true,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            ));

            $inputFilter->add(array(
            		'name'     => 'laenge',
            		'required' => false,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
       		));

            $inputFilter->add(array(
            		'name'     => 'breite',
            		'required' => false,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            ));

            $inputFilter->add(array(
                    'name'     => 'link',
                    'required' => false,
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
                                            'max'      => 200,
                                    ),
                            ),
                    ),
            ));
            
            $inputFilter->add(array(
                    'name'     => 'mapzoom',
                    'required' => false,
                    'filters'  => array(
                            array('name' => 'Int'),
                    ),
            ));
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
