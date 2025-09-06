<?php

class Scaffold_DefaultDb_Route extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_Route';

        $this->_columns['name'] = array(
            'label' => 'Nombre'
        );
        
        $this->_columns['code'] = array(
            'label' => 'Clave'
        );
        
        $this->_columns['factor'] = array(
            'label' => 'Factor'
        );
        
        $this->_externalActions['Puntos de venta'] = array(
            'module' => 'Admin',
            'controller' => 'Routes',
            'action' => 'points'
        );
        
        $this->_externalActions['Programación de rutas'] = array(
            'module' => 'Admin',
            'controller' => 'Schedule',
            'action' => 'route'
        );
        
        parent::__construct();
    }

}
