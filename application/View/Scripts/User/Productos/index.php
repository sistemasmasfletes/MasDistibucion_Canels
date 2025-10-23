<style>
.addbtn {
    /* Fondo y borde */
    background-color: #007bff; /* Azul primario */
    color: white; /* Texto blanco */
    border: 1px solid #007bff;
    border-radius: 5px;

    /* Espaciado y tipografía */
    padding: 8px 15px; /* Relleno interior */
    font-size: 14px;
    font-weight: 500;
    text-decoration: none; /* Quitar el subrayado del enlace */
    display: inline-flex; /* Alinear el ícono y el texto */
    align-items: center;
    gap: 5px; /* Espacio entre el ícono y el texto */

    /* Transición para efectos suaves */
    transition: background-color 0.3s, border-color 0.3s;
}

.addbtn:hover {
    background-color: #0056b3; /* Azul más oscuro al pasar el ratón */
    border-color: #0056b3;
}
</style>
<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Panel</li>
        <li class="active "> <a href="/public/User/Catalogos">Catálogos</a></li>
        <li class="active actualpg ">Productos</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Pasajeros del catalogo: <?php echo $view->catalog->getTitle();?> </h1>
                <?php
                echo '<a class="addbtn" href="' . $view->url(array('action' => 'add'),true) . '" ><i class="bi bi-person-add"></i>Nuevo pasajero</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->products != null)
                {
                    if(count($view->products) == 1 && $view->products[0]->getStatus() == 2)
                    { ?>
                        <div class="alert alert-info">
                        <strong>Actualmente no tiene productos asociados a este cat&aacute;logo</strong>, agr&eacute;gelos dando clic en el bot&oacute;n "Agregar Producto" ubicado en la parte superior.
                        </div> 
                        <?php
                    } 
                    else
                    {
                ?>
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Clasificacion1</th>
                            <th>Clasificacion2</th>
                            <th>Prioridad</th>
                            <!-- <th>Precio</th>
                            <th>Precio Normal</th>
                            <th>Stock</th>
                            <th>Destacado</th>
                            <th>SKU</th> -->
                            <th>Editar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($view->products as $p)
                            {
                                if($p->getStatus() == 1):
                                    ?>
                                 <tr>
                                    <td><?php echo $p->getIdPayroll();?></td>
                                    <td><?php echo $p->getName();?></td>
                                    <td><?php echo $p->getLastName();?></td>
                                    <td><?php echo $p->getClasificacion1();?></td>
                                    <td><?php echo $p->getClasificacion2();?></td>
                                    <td>
                                        <?php
                                        $priorityValue = $p->getPriority();

                                        if ($priorityValue == 0) {
                                            echo "Sin prioridad";
                                        } elseif ($priorityValue == 1) {
                                            echo "Prioridad media";
                                        } elseif ($priorityValue == 2) {
                                            echo "Máxima prioridad";
                                        } else {
                                            echo "No definido";
                                        }
                                        ?>
                                    </td>                                    
                                    <!-- <td><?php echo $p->getPrice();?></td>
                                    <td><?php echo $p->getPriceList();?></td>
                                    <td><?php
                                    if($p->getVariantsUse()==DefaultDb_Entities_Product::VARIANTS_NOT_USE)
                                        echo $p->getStock();
                                    else
                                        echo 'variantes';
                                    ?></td>

                                    <td><?php ?></td>
                                    <td><?php echo $p->getSku();?></td> -->
                                    <td>
                                        <a href="<?php echo $view->url(array('module'=>'User','controller'=>'Productos','action'=>'edit','id'=>$view->catalog->getId(),'idProduct'=>$p->getId()));?>" class="edit-link">
                                            Editar
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?php echo $view->url(array('module'=>'User','controller'=>'Productos','action'=>'delete','id'=>$view->catalog->getId(),'idProduct'=>$p->getId()));?>" class="delete-link">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                        endif;
                            }
                        ?>
                    </tbody>
                </table>
                <?php
                    }
                }
                else
                {
                    ?>
                    <div class="alert alert-info">
                        <strong>Actualmente no tiene productos asociados a este cat&aacute;logo</strong>, agr&eacute;gelos dando clic en el bot&oacute;n "Agregar Producto" ubicado en la parte superior.
                    </div>
                    <?php
               }
                ?>
            </div>
        <!--</div>-->
    </div>
</div>
