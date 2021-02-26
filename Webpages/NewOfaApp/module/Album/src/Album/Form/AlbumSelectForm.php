<?php
namespace Album\Form;

use Zend\Form\Form;

class AlbumSelectForm extends Form
{
    public function __construct($resultSetArtists)
    {
        parent::__construct();

        $this->add(array(
            'name' => 'ownerid',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                    '0' => 'Alle',
                    '1' => 'Jürgen',
                    '2' => 'Elke',
                ),
            ),
            'attributes' => array(
                'class' => 'lang',
            ),
        ));

        $this->add(array(
            'name' => 'roomid',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                    '0' => 'Alle',
                    '1' => 'Wohnzimmer',
                    '2' => 'Elkes Zimmer',
                    '3' => 'Schlafzimmer',
                    '4' => 'Bad',
                    '5' => 'Küche'
                ),
            ),
            'attributes' => array(
                'class' => 'lang',
            ),
        ));

        $selectDataArtist = array();

        $selectDataArtist['0'] = 'Alle Künstler';

        foreach ($resultSetArtists as $res)
        {
            $selectDataArtist[$res->albumartist] = $res->albumartist;
        }

        $this->add(array(
            'name' => 'albumartist',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                    'value_options' => $selectDataArtist,
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
            'name' => 'rating',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                        '0' => '0',
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
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
    }
}
