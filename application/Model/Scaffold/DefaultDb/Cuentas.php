<?php

class Scaffold_DefaultDb_Cuentas extends Model3_Scaffold_Model {

    public function __construct() {
        $this->_class = 'DefaultDb_Entities_Cuentas';
        
        
        $this->_columns['numeroCuenta'] = array(
            'label' => 'Numero de Cuenta'
        );
        
         $this->_columns['cuenta'] = array(
            'label' => 'Cuenta'
        );
        
        $this->_columns['moneda'] = array(
            'label' => 'Tipo Moneda',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_TipoMonedas',
                'key' => 'id',
                'display' => 'moneda'
            ));
        
        $this->_columns['name'] = array(
            'label' => 'Banco',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_Bancos',
                'key' => 'id',
                'display' => 'name'
            ));
        
        $this->_columns['nombre'] = array(
            'label' => 'Pais',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_Paises',
                'key' => 'id',
                'display' => 'nombre'
            ));
        
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