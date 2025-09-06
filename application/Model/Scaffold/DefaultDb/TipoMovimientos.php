<?php

class Scaffold_DefaultDb_TipoMovimientos extends Model3_Scaffold_Model {

    public function __construct() {
        $this->_class = 'DefaultDb_Entities_TipoMovimientos';

        $this->_columns['tipoMovimiento'] = array(
            'label' => 'Tipo de Movimiento'
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
