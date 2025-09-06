<?php

class Scaffold_DefaultDb_TipoPagos extends Model3_Scaffold_Model {

    public function __construct() {
        $this->_class = 'DefaultDb_Entities_TipoPagos';

        $this->_columns['tipoPago'] = array(
            'label' => 'Tipo de Pago'
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
