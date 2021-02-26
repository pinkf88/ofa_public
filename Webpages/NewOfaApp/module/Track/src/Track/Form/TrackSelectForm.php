<?php
namespace Track\Form;

use Zend\Form\Form;

class TrackSelectForm extends Form
{
    public function __construct($resultSetArtists, $resultSetAlbums)
    {
        parent::__construct();

        $selectDataArtist = array();
        $selectDataArtist['0'] = 'Alle Künstler';

        foreach ($resultSetArtists as $res)
        {
            $selectDataArtist[$res->albumartist] = $res->albumartist;
        }

        $selectDataAlbum = array();
        $selectDataAlbum['0'] = 'Alle Alben';

        foreach ($resultSetAlbums as $res)
        {
            $year = $res->originalyear;

            if ($res->year != $res->originalyear) {
                $year .= '/' . $res->year;
            }

            $selectDataAlbum[$res->musicbrainz_albumid] = $res->album . ' (' . $year . ')';
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
            'name' => 'album',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $selectDataAlbum,
            ),
            'attributes' => array(
                'class' => 'lang',
            ),
        ));

        $this->add(array(
            'name' => 'genre',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                    '0' => 'Alle',
                    '1' => 'Studio',
                    '2' => 'Live',
                ),
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
