<?php

class Scaffold_DefaultDb_TipoMonedas extends Model3_Scaffold_Model {

    public function __construct() {
        $this->_class = 'DefaultDb_Entities_TipoMonedas';

        $this->_columns['moneda'] = array(
            'label' => 'Nombre de la Monedas'
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
