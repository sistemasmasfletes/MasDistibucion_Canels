<?php

class Scaffold_DefaultDb_Vehicle extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_Vehicle';

        $this->_columns['name'] = array(
            'label' => 'Nombre'
        );        
        $this->_columns['capacity'] = array(
            'label' => 'Capacidad de carga (Kg)'
        );        
        $this->_columns['status'] = array(
            'label' => 'Activa',
            'enum' => array(
                0 => 'Inactiva',
                1 => 'Activa'
            )
        );        
        
        parent::__construct();
    }

}
