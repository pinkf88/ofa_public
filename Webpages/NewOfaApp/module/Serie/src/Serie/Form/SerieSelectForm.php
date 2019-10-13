<?php

namespace Serie\Form;

use Zend\Form\Form;

class SerieSelectForm extends Form
{

    public function __construct($resultSetWeb)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('SerieSelectForm->__construct().');
        
        parent::__construct();

        $selectDataWeb = array();
         
        foreach ($resultSetWeb as $res)
        {
            $selectDataWeb[$res->id] = $res->web;
        }

        $this->add(array(
            'name' => 'suchtext',
            'type' => 'Text',
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
                    '1000000' => 'Alle'
                )
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
                'id' => 'aktualisieren'
            )
        ));

        $this->add(array(
            'name' => 'webid',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $selectDataWeb,
            ),
        	'attributes' => array(
        		'class' => 'lang',
        	),
        ));
    }
}
