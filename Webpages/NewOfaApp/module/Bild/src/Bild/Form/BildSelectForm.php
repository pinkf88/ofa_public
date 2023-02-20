<?php
namespace Bild\Form;

use Zend\Form\Form;

class BildSelectForm extends Form
{
    public function __construct($resultSetJahre, $resultSetOrte, $resultSetLaender, $resultSetSerien)
    {
        parent::__construct();

        $selectDataJahre = array();

        $selectDataJahre['0'] = 'Alle Jahre';

        foreach ($resultSetJahre as $res) {
            $selectDataJahre[$res->jahr] = $res->jahr;
        }

        $selectDataOrte = array();

        $selectDataOrte['0'] = 'Alle Orte';

        foreach ($resultSetOrte as $res) {
            $selectDataOrte[$res->id] = $res->ort;
        }

        $selectDataLaender = array();

        $selectDataLaender['0'] = 'Alle Länder';

        foreach ($resultSetLaender as $res) {
            $selectDataLaender[$res->id] = $res->land;
        }

        $selectDataSerien = array();

        foreach ($resultSetSerien as $res) {
            $selectDataSerien[$res->id] = $res->serie;
        }

        $this->add(array(
            'name' => 'bildtyp',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                    '0' => 'Alle',
                    '1' => 'Nur Bilder',
                    '2' => 'Nur Tickets',
                    '3' => 'Nur Videos',
                ),
            ),
            'attributes' => array(
                'class' => 'mittel',
            ),
        ));

        $this->add(array(
            'name' => 'jahr',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $selectDataJahre,
            ),
            'attributes' => array(
                'class' => 'mittel',
            ),
        ));

        $this->add(array(
            'name' => 'ortid',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $selectDataOrte,
            ),
            'attributes' => array(
                'class' => 'lang',
            ),
        ));

        $this->add(array(
            'name' => 'landid',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $selectDataLaender,
            ),
            'attributes' => array(
                'class' => 'lang',
            ),
        ));

        $this->add(array(
            'name' => 'nummer_von',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'mittel',
            ),
        ));

        $this->add(array(
            'name' => 'nummer_bis',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'mittel',
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
            'name' => 'wertung_min',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                    '0' => 'Alle',
                    '1' => '1 und höher',
                    '2' => '2 und höher',
                    '3' => '3 und höher',
                    '4' => '4 und höher',
                    '5' => '5'
                ),
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

        $this->add(array(
            'name' => 'serieid',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $selectDataSerien,
            ),
            'attributes' => array(
                'class' => 'lang',
            ),
        ));
    }
}
