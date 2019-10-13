<?php

namespace Leben\Form;

use Zend\Form\Form;

class LebenForm extends Form
{

    public function __construct($resultSet)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LebenForm->__construct().');
        
        // we want to ignore the name passed
        parent::__construct('leben');
        
        $selectData = array();
        
        foreach ($resultSet as $res)
        {
            $selectData[$res->id] = $res->ort;
        }
        
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden'
        ));
        
        $this->add(array(
            'name' => 'datumvon',
            'type' => 'Zend\Form\Element\Date',
            'options' => array(
                'label' => 'Datum',
                ),
        	'attributes' => array(
				'autofocus' => 'autofocus',
            	),
            ));
        
        $this->add(array(
            'name' => 'nr',
            'type' => 'Hidden'
        ));
        
        $this->add(array(
            'name' => 'datumbis',
            'type' => 'Zend\Form\Element\Date',
            'options' => array(
                'label' => 'Datum',
            )
        ));
        
        $this->add(array(
            'name' => 'ortid',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Ort',
                'empty_option' => 'Bitte wählen',
                'value_options' => $selectData
            )
        ));
        
        $this->add(array(
            'name' => 'beschreibung',
            'type' => 'Text',
            'options' => array(
                'label' => 'Beschreibung'
            ),
            'attributes' => array(
                'class' => 'text'
            )
        ));
        
        $this->add(array(
            'name' => 'bemerkung',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Bemerkung'
            ),
            'attributes' => array(
                'class' => 'textarea'
            )
        ));
        
        $this->add(array(
            'name' => 'submit1',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton1',
                'accesskey' => 's'
            )
        ));
        
        $this->add(array(
            'name' => 'submit2',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton2',
                'accesskey' => 'n'
            )
        ));

        $this->add(array(
            'name' => 'submit3',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton3',
                'accesskey' => 'p'
            )
        ));
    }
}
