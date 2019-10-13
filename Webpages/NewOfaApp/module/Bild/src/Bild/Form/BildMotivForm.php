<?php
namespace Bild\Form;

use Zend\Form\Form;

class BildMotivForm extends Form
{
    public function __construct($resultSetMotive, $resultSetBildMotive)
    {
    	// $firephp = \FirePHP::getInstance(true);
    	// $firephp->log('BildMotivForm->__construct().');
    	 
    	parent::__construct();

    	foreach ($resultSetMotive as $res)
    	{
    		$found = false;
    		
    		foreach ($resultSetBildMotive as $res_bm)
    		{
    			if ($res_bm->motivid == $res->id)
    			{
    				$found = true;
    			
			    	$this->add(array(
		    			'type' => 'Zend\Form\Element\Checkbox',
		    			'name' => 'motiv' . $res->id . '#' . $res->motiv,
			    		'options' => array(
		   					'label' => $res->motiv,
		   					'use_hidden_element' => true,
		   					'checked_value' => '1',
		   					'unchecked_value' => '0',
		    			),
			    		'attributes' => array(
			    			'checked' => 'checked',
			    		),
			    	));

			    	break;
    			}
    		}
    		
    		if ($found == false)
    		{
    			$this->add(array(
   					'type' => 'Zend\Form\Element\Checkbox',
   					'name' => 'motiv' . $res->id . '#' . $res->motiv,
   					'options' => array(
						'label' => $res->motiv,
						'use_hidden_element' => true,
						'checked_value' => '1',
						'unchecked_value' => '0',
   					),
    			));
    		}
    	}
    	
		$this->add(array(
			'name' => 'aktualisieren',
			'type' => 'Zend\Form\Element\Button',
			'options' => array(
					'label' => 'Aktualisieren',
			),
       		'attributes' => array(
       			'id' => 'aktualisieren',
            	'accesskey' => 'a',
				'onclick'=>'bild_updateMotive()',
       		),
		));
    	
		// $firephp->log('BildMotivForm->__construct(). ENDE');
    }
}
