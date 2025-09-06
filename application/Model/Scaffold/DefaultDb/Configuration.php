<?php

class Scaffold_DefaultDb_Configuration extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_Configuration';

        $this->_columns['minutesPerPoint'] = array(
            'label' => 'Tiempo entre puntos'
        );

        $this->_deleteLink = false;
        parent::__construct();
    }

}
