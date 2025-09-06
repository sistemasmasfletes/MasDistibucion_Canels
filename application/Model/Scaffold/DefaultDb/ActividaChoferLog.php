<?php

class Scaffold_DefaultDb_ActividaChoferLog extends Model3_Scaffold_Model {

    public function __construct() {
        $this->_class = 'DefaultDb_Entities_ActividaChoferLog';
        $this->_columns['id'] = array(
            'label' => 'id',
             'formIgnore' => true
        );
        $this->_columns['tipoMovivimient'] = array(
            'label' => 'tipoMovivimient'
        );
        $this->_columns['fechaMov'] = array(
            'label' => 'fechaMov'
        );
        $this->_columns['metodoPago'] = array(
            'label' => 'metodoPago'
        );
        $this->_columns['referencia'] = array(
            'label' => 'referencia'
        );
        $this->_columns['concepto'] = array(
            'label' => 'concepto'
        );
        $this->_columns['estatus'] = array(
            'label' => 'estatus'
        );
        $this->_columns['valor'] = array(
            'label' => 'valor'
        );
        $this->_columns['saldoCaja'] = array(
            'label' => 'saldoCaja'
        );
        parent::__construct();
    }

}
