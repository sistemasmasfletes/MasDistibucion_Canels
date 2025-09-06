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
                <h1>Productos del catalogo: <?php echo $view->catalog->getTitle();?> </h1>
                <?php
                echo '<a class="btn btn-default sin-padding" href="' . $view->url(array('action' => 'add'),true) . '" ><span class="pers-btn icono-agregar-producto tam-normal"></span></a>';
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
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Precio Normal</th>
                            <th>Stock</th>
                            <th>Destacado</th>
                            <th>SKU</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($view->products as $p)
                            {
                                if($p->getStatus() == 1):
                                    ?>
                                 <tr>
                                    <td><?php echo $p->getName();?></td>
                                    <td><?php echo $p->getPrice();?></td>
                                    <td><?php echo $p->getPriceList();?></td>
                                    <td><?php
                                    if($p->getVariantsUse()==DefaultDb_Entities_Product::VARIANTS_NOT_USE)
                                        echo $p->getStock();
                                    else
                                        echo 'variantes';
                                    ?></td>

                                    <td><?php ?></td>
                                    <td><?php echo $p->getSku();?></td>
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
