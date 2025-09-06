<?php

class Scaffold_DefaultDb_TransferenciaCreditos extends Model3_Scaffold_Model {

    public function __construct() {
        $this->_class = 'DefaultDb_Entities_TransferenciaCreditos';
        
        $this->_columns['cliente'] = array(
            'label' => 'Cliente',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_User',
                'key' => 'id',
                'display' => 'username'
            ),
            //'formIgnore' => true,
            //'tableIgnore' => true
        );
        
        $this->_columns['fecha'] = array(
            'label' => 'Fecha(d-m-y)',
            'type' => 'datetime',
            'datetime' => array(
                'format' => 'd-m-Y '
            )
        );
        
        $this->_columns['creditos'] = array(
            'label' => 'No. de Creditos Existentes'
            //,'formIgnore' => true
        );
        
        $this->_columns['monto'] = array(
            'label' => 'Creditos a Transferir'
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
        $this->_columns['categoria'] = array(
            'label' => 'categoria',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_Category',
                'key' => 'id',
                'display' => 'name'
            ),
            'formIgnore' => true,
            //'tableIgnore' => true
        );
        
        parent::__construct();
    }
}