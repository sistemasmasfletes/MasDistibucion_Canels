<?php

class Scaffold_DefaultDb_CompraCreditos extends Model3_Scaffold_Model {

    public function __construct() {
        $this->_class = 'DefaultDb_Entities_CompraCreditos';
        
        $this->_columns['client'] = array(
            'label' => 'Cliente',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_User',
                'key' => 'id',
                'display' => 'username'
            ),
            //'formIgnore' => true,
            //'tableIgnore' => true
        );
        
        $this->_columns['tipoPago'] = array(
            'label' => 'Metodo de Pago',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_TipoPagos',
                'key' => 'id',
                'display' => 'tipoPago'
            ));
        
        $this->_columns['montoCompra'] = array(
            'label' => 'Monto de Compra'
        );
        
        $this->_columns['creditos'] = array(
            'label' => 'No. de Creditos'
            ,'formIgnore' => true
        );
        
        $this->_columns['fecha'] = array(
            'label' => 'Fecha(d-m-y)',
            'type' => 'datetime',
            'datetime' => array(
                'format' => 'd-m-Y '
            )
        );
        
        $this->_columns['nombreImg'] = array(
            'label' => 'Nombre Imagen'
        );
        
        $this->_columns['path'] = array(
            'label' => 'Direccion'
            ,'formIgnore' => true
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
        
        $this->_columns['referencia'] = array(
            'label' => 'Referencia'
        );
        
        $this->_columns['cuenta'] = array(
            'label' => 'No.Cuenta'
        );
        
       $this->_columns['estado'] = array(
            'label' => 'Estado'
            ,'formIgnore' => true
        );
        
        $this->_columns['client'] = array(
            'label' => 'Cliente',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_User',
                'key' => 'id',
                'display' => 'username'
            ),
            'formIgnore' => true,
            //'tableIgnore' => true
        );
        
        parent::__construct();
    }
}