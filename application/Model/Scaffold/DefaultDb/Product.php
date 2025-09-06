<?php

class Scaffold_DefaultDb_Product extends Model3_Scaffold_Model
{

    public function __construct()
    {
        $this->_class = 'DefaultDb_Entities_Product';

//        $this->_columns['client'] = array(
//            'label' => 'Cliente',
//            'relation' => array(
//                'entity' => 'DefaultDb_Entities_User',
//                'key' => 'id',
//                'display' => 'username'
//                ),
//            'formIgnore' => true,
//            'tableIgnore' => true
//        );
//
        $this->_columns['name'] = array(
            'label' => 'Nombre'
        );

        $this->_columns['price'] = array(
            'label' => 'Precio'
        );

        $this->_columns['priceList'] = array(
            'label' => 'Precio Normal'
        );


        $this->_columns['catalog'] = array(
            'label' => 'Catalogo',
            'relation' => array(
                'entity' => 'DefaultDb_Entities_Catalog',
                'key' => 'id',
                'display' => 'title'
                ),
            'formIgnore' => true,
            'tableIgnore' => true
        );
        
        $this->_columns['stock'] = array(
            'label' => 'Stock'
        );

        $this->_columns['featured'] = array(
            'label' => 'Destacado',
            'formIgnore' => true,
             'enum' => array(
                0 => 'No destacar',
                1 => 'Destacar'
            )
        );

        $this->_columns['order'] = array(
            'label' => 'Orden',
            'formIgnore' => true,
            'tableIgnore' => true
        );

        $this->_columns['description'] = array(
            'label' => 'Description',
            'formIgnore' => true,
            'tableIgnore' => true
        );

        $this->_columns['offer'] = array(
            'label' => 'Oferta',
            'formIgnore' => true,
            'tableIgnore' => true,
             'enum' => array(
                0 => 'No',
                1 => 'Si'
            )
        );

        $this->_columns['maker'] = array(
            'label' => 'Fabricante',
            'formIgnore' => true,
            'tableIgnore' => true
        );

        $this->_columns['provitionTime'] = array(
            'label' => 'Tiempo de disponibilidad',
            'formIgnore' => true,
            'tableIgnore' => true
        );

        $this->_columns['newStartDate'] = array(
            'label' => 'Nuevo desde',
            'type' => 'date',
            'datetime' => array(
                'format' => 'Y-m-d h:i:s'
            ),
            'formIgnore' => true,
            'tableIgnore' => true
        );

        $this->_columns['newEndDate'] = array(
            'label' => 'Nuevo hasta',
            'type' => 'date',
            'datetime' => array(
                'format' => 'Y-m-d h:i:s'
            ),
            'formIgnore' => true,
            'tableIgnore' => true
        );

        $this->_columns['sku'] = array(
            'label' => 'SKU',
        );

        $this->_columns['warranty'] = array(
            'label' => 'Garantia',
            'formIgnore' => true,
            'tableIgnore' => true
        );

        $this->_columns['weight'] = array(
            'label' => 'Peso',
            'formIgnore' => true,
            'tableIgnore' => true
        );

        $this->_columns['color'] = array(
            'label' => 'Color',
            'formIgnore' => true,
            'tableIgnore' => true
        );

//        $this->_externalActions['Productos'] = array(
//            'module' => 'User',
//            'controller' => 'Productos',
//            'action' => 'index'
//        );

        parent::__construct();
    }

}
