<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Rutas</li>
    <li class="active "><?php echo '<a href="' . $view->url(array('action' => 'index')) . '" >Calendario</a>'?></li>
    <li class="active actualpg ">Eliminar Calendario</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
               <?php 
                echo '<div class="alert alert-success">';
                echo 'El calendario se elimino correctamente';
                echo '</div>';?>
            </div>
        <!--</div>-->
    </div>
</div>