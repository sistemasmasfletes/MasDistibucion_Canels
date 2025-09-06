<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Catálogos</li>
    <li class="active "><?php echo '<a href="' . $view->url(array('action' => 'index')) . '" >Categorías</a>'?></li>
    <li class="active actualpg ">Eliminar Categoría</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
               <?php 
                echo '<div class="alert alert-success">';
                echo 'La categoria se elimino correctamente';
                echo '</div>';?>
            </div>
        <!--</div>-->
    </div>
</div>