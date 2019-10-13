<?php

namespace Web\Form;

use Zend\Form\Form;

class WebSelectForm extends Form
{

    public function __construct()
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('WebSelectForm->__construct().');
        
        parent::__construct();

        $this->add(array(
            'name' => 'countperpage',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
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
    }
}
