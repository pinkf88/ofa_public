<?php
namespace Serie\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;

class SerieForm extends Form
{
	protected $dbAdapter;
	
    public function __construct($name = null, AdapterInterface $dbAdapter = null)
    {
    	$this->setDbAdapter($dbAdapter);
    	
        // we want to ignore the name passed
        parent::__construct('serie');

        $this->add(array(
                'name' => 'id',
                'type' => 'Hidden',
            ));
            
        $this->add(array(
            'name' => 'serie',
            'type' => 'Text',
            'options' => array(
                'label' => 'Serie',
             ),
        	'attributes' => array(
				'autofocus' => 'autofocus',
        		'class' => 'langtext',
        	),
        ));

        $this->add(array(
                'name' => 'zusatz',
                'type' => 'Textarea',
                'options' => array(
                        'label' => 'Zusatz',
                ),
                'attributes' => array(
                        'class' => 'textarea',
                ),
        ));

        $this->add(array(
		    'type' => 'Zend\Form\Element\Checkbox',
		    'name' => 'link_serie',
		    'options' => array(
		        'label' => 'Link Serie',
		        'use_hidden_element' => true,
		        'checked_value' => '1',
		        'unchecked_value' => '0'
		    )
		));

        $this->add(array(
		    'type' => 'Zend\Form\Element\Checkbox',
		    'name' => 'link_land',
		    'options' => array(
		        'label' => 'Link Land',
		        'use_hidden_element' => true,
		        'checked_value' => '1',
		        'unchecked_value' => '0'
		    )
		));

        $this->add(array(
		    'type' => 'Zend\Form\Element\Checkbox',
		    'name' => 'link_ort',
		    'options' => array(
		        'label' => 'Link Ort',
		        'use_hidden_element' => true,
		        'checked_value' => '1',
		        'unchecked_value' => '0'
		    )
		));

        $this->add(array(
		    'type' => 'Zend\Form\Element\Checkbox',
		    'name' => 'link_motiv',
		    'options' => array(
		        'label' => 'Link Motiv',
		        'use_hidden_element' => true,
		        'checked_value' => '1',
		        'unchecked_value' => '0'
		    )
		));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
            'value' => 'Go',
            'id' => 'submitbutton',
            ),
        ));
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
