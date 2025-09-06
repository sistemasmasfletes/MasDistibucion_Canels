<?php

class Scaffold_DefaultDb_Calendar extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_Calendar';

        $this->_columns['startDate'] = array(
            'label' => 'Fecha de inicio',
            'type' => 'datetime',
            'datetime' => array(
                'format' => 'd-m-Y '
            )
        );
        $this->_columns['endDate'] = array(
            'label' => 'Fecha de terminacion',
            'type' => 'datetime',
            'datetime' => array(
                'format' => 'd-m-Y '
            )
        );

        $this->_columns['route'] = array(
            'label' => 'Ruta',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_Route',
                'key' => 'id',
                'display' => 'name'
            )
        );

        $this->_columns['description'] = array(
            'label' => 'Descripci&oacute;n',
            'type' => 'string'
        );

        $this->_columns['status'] = array(
            'label' => 'Estatus',
            'enum' => array(
                DefaultDb_Entities_Calendar::STATUS_ACTIVE => 'Activo',
                DefaultDb_Entities_Calendar::STATUS_INACTIVE  => 'Desactivado',
                DefaultDb_Entities_Calendar::STATUS_DELETED  => 'Eliminado'
            )
        );

        parent::__construct();
    }

}
