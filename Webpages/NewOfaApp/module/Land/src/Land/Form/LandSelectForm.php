<?php
namespace Land\Form;

use Zend\Form\Form;

class LandSelectForm extends Form
{
    public function __construct($resultSet)
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('LandSelectForm->__construct().');
    	
    	parent::__construct();
    	
    	$selectData = array();
    	
    	$selectData['0'] = 'Alle Kontinente';
    	
    	foreach ($resultSet as $res)
    	{
    		$selectData[$res->id] = $res->kontinent;
    	}

		$this->add(array(
			'name' => 'kontinentid',
			'type' => 'Zend\Form\Element\Select',
			'options' => array(
                'value_options' => $selectData,
			),
        	'attributes' => array(
        		'class' => 'mittel',
        	),
		));

		$this->add(array(
			'name' => 'countperpage',
			'type' => 'Zend\Form\Element\Select',
			'options' => array(
				'value_options' => array(
		            '250' => '250',
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