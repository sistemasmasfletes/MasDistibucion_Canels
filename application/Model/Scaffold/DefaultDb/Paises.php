<?php

class Scaffold_DefaultDb_Paises extends Model3_Scaffold_Model {

    public function __construct() {
        $this->_class = 'DefaultDb_Entities_Paises';

        $this->_columns['nombre'] = array(
            'label' => 'Nombre del Pais'
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
