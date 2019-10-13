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
            $selectDataAlbum[$res->musicbrainz_albumid] = $res->album . ' (' . $res->year . ')';
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
                        '200' => '200',
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
