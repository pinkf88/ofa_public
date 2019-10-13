<?php
namespace Ort\Form;

use Zend\Form\Form;

class OrtSelectForm extends Form
{
    public function __construct($resultSet)
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('OrtSelectForm->__construct().');
    	
    	parent::__construct();
    	
    	$selectData = array();
    	
    	$selectData['0'] = 'Alle Länder';
    	
    	foreach ($resultSet as $res)
    	{
    		$selectData[$res->id] = $res->land;
    	}

		$this->add(array(
			'name' => 'landid',
			'type' => 'Zend\Form\Element\Select',
			'options' => array(
                'value_options' => $selectData,
			),
        	'attributes' => array(
        		'class' => 'lang',
        	),
		));

		$this->add(array(
			'name' => 'countperpage',
			'type' => 'Zend\Form\Element\Select',
			'options' => array(
				'value_options' => array(
		            '50' => '50',
		            '200' => '200',
		            '1000' => '1000',
		            '1000000' => 'Alle',
			     ),
			),
        	'attributes' => array(
        		'class' => 'mittel',
        	),
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Aktualisieren',
				'id' => 'aktualisieren',
			),
		));
    }
}