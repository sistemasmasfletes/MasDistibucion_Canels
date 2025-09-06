<?php

class Scaffold_DefaultDb_PackageTypes extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_PackageTypes';

        $this->_columns['name'] = array(
            'label' => 'Nombre'
        );

        $this->_columns['price'] = array(
            'label' => 'Precio'
        );

        $this->_columns['height'] = array(
            'label' => 'Alto'
        );

        $this->_columns['width'] = array(
            'label' => 'Ancho'
        );

        $this->_columns['depth'] = array(
            'label' => 'Profundidad'
        );

        $this->_columns['weight'] = array(
            'label' => 'Peso maximo'
        );





        parent::__construct();
    }

}
