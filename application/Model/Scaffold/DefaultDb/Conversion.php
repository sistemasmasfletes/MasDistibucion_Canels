<?php

class Scaffold_DefaultDb_Conversion extends Model3_Scaffold_Model {

    public function __construct() {
        $this->_class = 'DefaultDb_Entities_Conversion';
        
       $this->_columns['moneda'] = array(
            'label' => 'Tipo Moneda',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_TipoMonedas',
                'key' => 'id',
                'display' => 'moneda'
            ));
       
        $this->_columns['compra'] = array(
            'label' => 'Compra'
        );

        $this->_columns['venta'] = array(
            'label' => 'Venta'
        );
        
        $this->_columns['creditos'] = array(
            'label' => 'Total de Creditos'
        );
        
        $this->_columns['fecha'] = array(
            'label' => 'Fecha(d-m-y)',
            'type' => 'datetime',
            'datetime' => array(
                'format' => 'd-m-Y '
            )
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