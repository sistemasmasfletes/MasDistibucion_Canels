<?php

class Scaffold_DefaultDb_Aprobacion extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_Aprobacion';

        
        $this->_columns['estado'] = array(
            'label' => 'Estado'
        ); 
        
        $this->_columns['client'] = array(
            'label' => 'Cliente',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_User',
                'key' => 'id',
                'display' => 'username'
            ),
            'formIgnore' => true,
            'tableIgnore' => true
        );
        
        parent::__construct();
    }

}

