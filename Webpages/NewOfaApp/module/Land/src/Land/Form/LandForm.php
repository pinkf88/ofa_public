<?php

namespace Land\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;

class LandForm extends Form
{
    protected $dbAdapter;

    public function __construct($name = null, AdapterInterface $dbAdapter = null)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->log('LandForm->__construct(). $name=' . $name);
        
        $this->setDbAdapter($dbAdapter);
        
        // we want to ignore the name passed
        parent::__construct('land');
        
        $this->add(array(
                'name' => 'id',
                'type' => 'Hidden'
        ));
        
        $this->add(array(
                'name' => 'land',
                'type' => 'Text',
                'options' => array(
                        'label' => 'Land'
                ),
                'attributes' => array(
                        'autofocus' => 'autofocus',
                        'class' => 'langtext'
                )
        ));
        
        $this->add(array(
                'name' => 'kurz',
                'type' => 'Text',
                'options' => array(
                        'label' => 'Kurz'
                )
        ));
        
        $this->add(array(
                'name' => 'kontinentid',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                        'label' => 'Kontinent',
                        'empty_option' => 'Bitte wählen',
                        'value_options' => $this->getOptionsForSelect()
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
        
        $firephp->log('LandForm->__construct(). ENDE');
    }

    protected function setDbAdapter($dbA)
    {
        $this->dbAdapter = $dbA;
    }

    protected function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    public function getOptionsForSelect()
    {
        $dbAdapter = $this->getDbAdapter();
        $sql = 'SELECT id, kontinent FROM ofa_kontinent ORDER BY kontinent ASC';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        
        $selectData = array();
        
        foreach ($result as $res)
        {
            $selectData[$res['id']] = $res['kontinent'];
        }
        
        return $selectData;
    }
}
