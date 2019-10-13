<?php
namespace Ort\Form;

use Zend\Form\Form;

class OrtForm extends Form
{
    public function __construct($resultSetLand)
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('OrtForm->__construct().');
    	
        // we want to ignore the name passed
        parent::__construct('ort');

        $selectDataLand = array();
         
        foreach ($resultSetLand as $res)
        {
        	$selectDataLand[$res->id] = $res->land;
        }
        
        $this->add(array(
                'name' => 'id',
                'type' => 'Hidden',
            ));
            
        $this->add(array(
            'name' => 'ort',
            'type' => 'Text',
            'options' => array(
                'label' => 'Ort',
            	),
        	'attributes' => array(
				'autofocus' => 'autofocus',
        		'class' => 'langtext',
        	),
        ));

        $this->add(array(
            'name' => 'landid',
            'type' => 'Zend\Form\Element\Select',
        	'options' => array(
                'label' => 'Land',
        		'empty_option' => 'Bitte wählen',
            	'value_options' => $selectDataLand,
        	),
    	));

        $this->add(array(
    		'name' => 'breite',
    		'type' => 'Text',
    		'options' => array(
				'label' => 'Breite',
    		),
        ));

        $this->add(array(
    		'name' => 'laenge',
    		'type' => 'Text',
    		'options' => array(
				'label' => 'Länge',
    		),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
        
        $firephp->log('OrtForm->__construct(). ENDE');
    }
}
