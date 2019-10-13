<?php
namespace Motiv\Form;

use Zend\Form\Form;

class MotivForm extends Form
{
    public function __construct($resultSet)
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('MotivForm->__construct().');
    	
        // we want to ignore the name passed
        parent::__construct('motiv');

        $selectData = array();
         
        foreach ($resultSet as $res)
        {
        	$selectData[$res->id] = $res->ort;
        }
        
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
            
        $this->add(array(
            'name' => 'motiv',
            'type' => 'Text',
            'options' => array(
                'label' => 'Motiv',
        	),
        	'attributes' => array(
				'autofocus' => 'autofocus',
        		'class' => 'langtext',
        	),
        ));

        $this->add(array(
            'name' => 'ortid',
            'type' => 'Zend\Form\Element\Select',
        	'options' => array(
                'label' => 'Ort',
        		'empty_option' => 'Bitte wählen',
            	'value_options' => $selectData,
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
                'name' => 'link',
                'type' => 'Text',
                'options' => array(
                        'label' => 'Link',
                ),
            	'attributes' => array(
            		'class' => 'langtext',
            	),
        ));
        
        $this->add(array(
                'name' => 'mapzoom',
                'type' => 'Text',
                'options' => array(
                        'label' => 'Map Zoom',
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
        
        $firephp->log('MotivForm->__construct(). ENDE');
    }
}
