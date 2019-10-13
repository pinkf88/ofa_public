<?php

namespace Label\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;

class LabelForm extends Form
{
    protected $dbAdapter;

    public function __construct($name = null, AdapterInterface $dbAdapter = null)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LabelForm->__construct(). $name=' . $name);
        
        $this->setDbAdapter($dbAdapter);
        
        // we want to ignore the name passed
        parent::__construct('label');
        
        $this->add(array(
                'name' => 'id',
                'type' => 'Hidden'
        ));
        
        $this->add(array(
                'name' => 'label_en',
                'type' => 'Text',
                'options' => array(
                        'label' => 'Label (Englisch)'
                ),
                'attributes' => array(
                        'autofocus' => 'autofocus',
                        'class' => 'langtext'
                )
        ));
        
        $this->add(array(
                'name' => 'label_de',
                'type' => 'Text',
                'options' => array(
                        'label' => 'Label (Deutsch)'
                ),
                'attributes' => array(
                        'class' => 'langtext'
                )

        ));
        
        $this->add(array(
                'name' => 'used',
                'type' => 'Text',
                'options' => array(
                        'label' => 'Used'
                )
        ));
                
        $this->add(array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                        'value' => 'Go',
                        'id' => 'submitbutton'
                )
        ));
        
        $firephp->log('LabelForm->__construct(). ENDE');
    }

    protected function setDbAdapter($dbA)
    {
        $this->dbAdapter = $dbA;
    }

    protected function getDbAdapter()
    {
        return $this->dbAdapter;
    }
}
