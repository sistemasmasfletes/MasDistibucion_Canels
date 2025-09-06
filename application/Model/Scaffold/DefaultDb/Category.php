<?php

class Scaffold_DefaultDb_Category extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_Category';

        $this->_columns['name'] = array(
            'label' => 'Nombre'
        );
        $this->_columns['imagePath'] = array(
            'tableIgnore' => true,
            'formIgnore' => true
        );

        parent::__construct();
    }

}
