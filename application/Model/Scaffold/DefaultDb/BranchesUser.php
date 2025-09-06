<?php

class Scaffold_DefaultDb_BranchesUser extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_BranchesUser';

       $this->_columns['name'] = array(
            'label' => 'Nombre',
            'type' => 'string'
        );

        $this->_columns['direction'] = array(
            'label' => 'Direcci&oacute;n',
            'type' => 'string'
        );     
        
        $this->_columns['client'] = array(
            'label' => 'client',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_User',
                'key' => 'id',
                'display' => 'title'
                ),
            'formIgnore' => true,
            'tableIgnore' => true
            
        );

        parent::__construct();
    }

}
