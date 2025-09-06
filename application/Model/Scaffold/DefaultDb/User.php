<?php

class Scaffold_DefaultDb_User extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_User';

        $this->_columns['code'] = array(
            'label' => 'Clave'
        );
        $this->_columns['firstName'] = array(
            'label' => 'Nombre'
        );
        $this->_columns['lastName'] = array(
            'label' => 'Apellidos'
        );
        $this->_columns['username'] = array(
            'label' => 'Usuario'
        );
        $this->_columns['password'] = array(
            'label' => 'Password',
            'type' => 'password',
            'tableIgnore' => true
        );
        $this->_columns['type'] = array(
            'label' => 'Tipo de Usuario',
            'enum' => array(
                DefaultDb_Entities_User::USER_ADMIN => 'Administrador',
                DefaultDb_Entities_User::USER_DRIVER  => 'Conductor',
                DefaultDb_Entities_User::USER_CLIENT  => 'Cliente',
                DefaultDb_Entities_User::USER_SECRETARY  => 'Secretaria'
            )
        );
        $this->_columns['commercialName'] = array(
            'label' => 'Nombre Comercial',
        );
        $this->_columns['category'] = array(
            'label' => 'Categoria',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_Category',
                'key' => 'id',
                'display' => 'name'
            )
        );
        $this->_columns['point'] = array(
            'label' => 'Punto de Venta',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_Point',
                'key' => 'id',
                'display' => 'name'
            ),
            'tableIgnore' => true
        );
        $this->_externalActions['Reporte'] = array(
            'module' => 'Admin',
            'controller' => 'ReportsUsers',
            'action' => 'index'
        );

        parent::__construct();
    }

}
