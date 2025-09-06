<?php

class Scaffold_DefaultDb_Estatus extends Model3_Scaffold_Model
{
    
    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_Estatus';

        $this->_columns['Estatus'] = array(
            'label' => 'Estatus'
        );
        
        $this->_columns['client'] = array(
            'label' => 'username',
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