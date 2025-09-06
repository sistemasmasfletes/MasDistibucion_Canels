<style>
    .block
    {
        display: block;
    }

   .inline
    {
        display: inline;
    }

    #productForm textarea
    {
        width: 650px;
        height: 150px;
    }
</style>
<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Panel</li>
        <li class="active "> <?php echo '<a href="' . $view->getBaseUrlPublic() . "/User/Catalogos" . '" >Catálogos</a>'; ?></li>
        <li class="active "> <?php echo '<a href="' . $view->url(array('action' => 'index'), true) . '" >Productos</a>'; ?></li>
        <li class="active actualpg ">Agregar Producto</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <!--<div class="blockInner">-->
                <h1>Alta de producto</h1>
                <?php
//                echo '<a class="btn" href="' . $view->url(array('action' => 'index'),true) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                ?>
                <?php  $view->Products()->printProductForm( $view->toSave,$view->catalogs,$view->catalog, null ,$view->creditos);?>
            <!--</div>-->    
        <!--</div>-->
    </div>
</div>
