<?php

class Scaffold_DefaultDb_Catalog extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_Catalog';

        $this->_columns['client'] = array(
            'label' => 'Cliente',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_User',
                'key' => 'id',
                'display' => 'username'
                ),
            'formIgnore' => true,
            'tableIgnore' => true
        );

        $this->_columns['title'] = array(
            'label' => 'Titulo'
        );

        $this->_columns['catalogFather'] = array(
            'label' => 'Catalogo padre',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_Catalog',
                'key' => 'id',
                'display' => 'title'
                )
        );
        
        $this->_columns['urlCatalog'] = array(
            'label' => 'Url del Catalogo'
        );
        
        $this->_externalActions['Productos'] = array(
            'module' => 'User',
            'controller' => 'Productos',
            'action' => 'index'
        );

        parent::__construct();
    }

}
