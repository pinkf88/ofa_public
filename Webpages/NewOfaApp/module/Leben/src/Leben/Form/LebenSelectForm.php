<?php

namespace Leben\Form;

use Zend\Form\Form;

class LebenSelectForm extends Form
{
    public function __construct($resultSetJahre, $resultSetOrte, $resultSetLaender)
    {
        parent::__construct();

        $selectDataJahre = array();
        $selectDataJahre['0'] = 'Alle Jahre';

        foreach ($resultSetJahre as $res)
        {
            // $firephp->log('LebenSelectForm->__construct(). jahr=' . $res->jahr);
            $selectDataJahre[$res->jahr] = $res->jahr;
        }

        $selectDataOrte = array();
        $selectDataOrte['0'] = 'Alle Orte';

        foreach ($resultSetOrte as $res)
        {
            $selectDataOrte[$res->id] = $res->ort;
        }

        $selectDataLaender = array();
        $selectDataLaender['0'] = 'Alle Länder';

        foreach ($resultSetLaender as $res)
        {
            $selectDataLaender[$res->id] = $res->land;
        }

        $this->add(array(
            'name' => 'jahr',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $selectDataJahre
            ),
        	'attributes' => array(
        		'class' => 'mittel',
        	),
        ));

        $this->add(array(
            'name' => 'ortid',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $selectDataOrte
            ),
        	'attributes' => array(
        		'class' => 'lang',
        	),
        ));

        $this->add(array(
            'name' => 'landid',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $selectDataLaender
            ),
        	'attributes' => array(
        		'class' => 'lang',
        	),
        ));

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
                    '250' => '250',
                    '2000' => '1000',
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
    }
}
