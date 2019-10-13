<?php
namespace Album\Form;

use Zend\Form\Form;

class AlbumForm extends Form
{
    public function __construct($resultSet)
    {
        parent::__construct('album');

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
            'name' => 'nummer',
            'type' => 'Text',
            'options' => array(
                'label' => 'Nummer',
                ),
        	'attributes' => array(
				'autofocus' => 'autofocus',
        		'class' => 'number',
            	),
            ));

        $this->add(array(
        	'name' => 'datei',
        	'type' => 'Text',
        	'options' => array(
       			'label' => 'Datei',
        		),
        	'attributes' => array(
        		'class' => 'number',
            	),
        	));

        $this->add(array(
        		'name' => 'datum',
			    'type' => 'Zend\Form\Element\Date',
        		'options' => array(
        			'label' => 'Datum',
             		// 'format' => 'd.m.Y',
        		),
        ));

        $this->add(array(
		    'type' => 'Zend\Form\Element\Checkbox',
		    'name' => 'jahrflag',
		    'options' => array(
		        'label' => 'Nur Jahr',
		        'use_hidden_element' => true,
		        'checked_value' => '1',
		        'unchecked_value' => '0'
		    )
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
       		'name' => 'beschreibung',
       		'type' => 'Text',
       		'options' => array(
   				'label' => 'Beschreibung',
       		),
        	'attributes' => array(
        		'class' => 'text',
            	),
       	));

        $this->add(array(
       		'name' => 'bemerkung',
       		'type' => 'Textarea',
       		'options' => array(
   				'label' => 'Bemerkung',
       		),
        	'attributes' => array(
        		'class' => 'textarea',
            	),
       	));

        $this->add(array(
		    'type' => 'Zend\Form\Element\Checkbox',
		    'name' => 'panorama',
		    'options' => array(
		        'label' => 'Panorama',
		        'use_hidden_element' => true,
		        'checked_value' => '1',
		        'unchecked_value' => '0'
		    )
		));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'ticket',
            'options' => array(
                'label' => 'Ticket',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));

        $this->add(array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'ohneort',
                'options' => array(
                        'label' => 'Ohne Ort',
                        'use_hidden_element' => true,
                        'checked_value' => '1',
                        'unchecked_value' => '0'
                )
        ));

        $this->add(array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'ohneland',
                'options' => array(
                        'label' => 'Ohne Land',
                        'use_hidden_element' => true,
                        'checked_value' => '1',
                        'unchecked_value' => '0'
                )
        ));

        $this->add(array(
            'name' => 'submit1',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton1',
            	'accesskey' => 's',
                ),
            ));

        $this->add(array(
       		'name' => 'submit2',
       		'type' => 'Submit',
       		'attributes' => array(
       			'value' => 'Go',
       			'id' => 'submitbutton2',
            	'accesskey' => 'n',
       		),
        ));
    }
}
