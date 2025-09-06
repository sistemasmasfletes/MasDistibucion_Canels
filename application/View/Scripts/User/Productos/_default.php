<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Panel</li>
        <li class="active "> <?php echo '<a href="' . $view->getBaseUrlPublic() . "/User/Catalogos" . '" >Catálogos</a>'; ?></li>
        <li class="active "> <?php echo '<a href="' . $view->url(array('action' => 'index'), true) . '" >Productos</a>'; ?></li>
        <li class="active actualpg ">Eliminar Producto</li>
    </ol>
</div>

<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <?php
 
//                echo '<a class="btn" href="' . $view->url(array('action' => 'index'),true) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->product):?>
                <div class="alert alert-success">
                El producto <?php echo $view->product->getName();?> se elimino correctamente
                </div>
                <?php else: ?>
                <div class="alert alert-error">
                El producto <?php echo $view->product->getName();?> no se pudo eliminar por favor intentelo mas tarde
                </div>
                <?php endif; ?>
            </div>    
        <!--</div>-->
    </div>
</div>
