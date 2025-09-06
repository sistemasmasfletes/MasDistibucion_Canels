<?php
class Scaffold_DefaultDb_Point extends Model3_Scaffold_Model
{
    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_Point';

        $this->_columns['name'] = array(
            'label' => 'Nombre'
        );
        
        $this->_columns['code'] = array(
            'label' => 'Clave'
        );
        
        $this->_columns['type'] = array(
            'label' => 'Tipo',
            'enum' => array(
                DefaultDb_Entities_Point::TYPE_SALE_POINT => 'Punto de venta',
                DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER => 'Centro de intercambio'
            )
        );
        
        $this->_columns['status'] = array(
            'label' => 'Estatus',
            'enum' => array(
                DefaultDb_Entities_Point::STATUS_NORMAL => 'Normal',
                DefaultDb_Entities_Point::STATUS_PAUSED => 'En pausa',
                DefaultDb_Entities_Point::STATUS_CANCELED => 'Cancelado'
            )
        );
        
        $this->_columns['address'] = array(
            'label' => 'Direccion'
        );
        parent::__construct();
    }

}
