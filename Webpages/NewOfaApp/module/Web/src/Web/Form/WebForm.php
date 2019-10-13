<?php
namespace Web\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;

class WebForm extends Form
{
	protected $dbAdapter;
	
    public function __construct($resultSetLand)
    {
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('WebForm->__construct()');
    	
        // we want to ignore the name passed
        parent::__construct('web');

        $selectDataLand = array();
         
        $selectDataLand[0] = 'Bitte wählen';
        
        foreach ($resultSetLand as $res)
        {
            $selectDataLand[$res->id] = $res->land;
        }
        
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'landid',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Land',
//                 'empty_option' => 'Bitte wählen',
                'value_options' => $selectDataLand,
            ),
        ));
        
        $this->add(array(
            'name' => 'web',
            'type' => 'Text',
            'options' => array(
            'label' => 'Webgruppe',
            ),
        ));

        $this->add(array(
            'name' => 'pfad',
            'type' => 'Text',
            'options' => array(
                'label' => 'Pfad',
            ),
            'attributes' => array(
                'class' => 'text',
            ),
        ));
        
        $this->add(array(
            'name' => 'zusatz1',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Zusatz 1',
            ),
            'attributes' => array(
                'class' => 'textarea',
            ),
        ));

        $this->add(array(
            'name' => 'zusatz2',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Zusatz 2',
            ),
            'attributes' => array(
                'class' => 'textarea',
            ),
        ));

        $this->add(array(
            'name' => 'nummer',
            'type' => 'Text',
            'options' => array(
                'label' => 'Nummer',
            ),
            'attributes' => array(
            	'class' => 'number',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
            'value' => 'Go',
            'id' => 'submitbutton',
            ),
        ));
        
        $firephp->log('WebForm->__construct(). ENDE');
    }
}
