<?php

class Scaffold_DefaultDb_FavoriteUsers extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_FavoriteUsers';

        $this->_columns['favoriteClient'] = array(
            'label' => 'Favorito',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_User',
                'key' => 'id',
                'display' => 'commercialName'
                )
        );

        $this->_editLink = false;
        parent::__construct();
    }

}
