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
    	$firephp = \FirePHP::getInstance(true);
    	$firephp->log('SerieForm->__construct(). $name=' . $name);
    	
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
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
            'value' => 'Go',
            'id' => 'submitbutton',
            ),
        ));
        
        $firephp->log('SerieForm->__construct(). ENDE');
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
