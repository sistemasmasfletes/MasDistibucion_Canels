<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Panel</li>
        <li class="active "> <?php echo '<a href="' . $view->url(array('action' => 'index')) . '" >Catálogos</a>'; ?></li>
        <li class="active actualpg ">Eliminar catálogo</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
        <div class="blockInner">
            <?php
//                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a><br/><br/>';
            if ($view->result == true) {
                echo '<div class="alert alert-success">';
                echo 'El c&aacute;talogo ha sido eliminado';
                echo '</div>';
            } else if ($view->isFather == false) {
                if ($view->products == true)
                    echo '¿Esta seguro de desear eliminar este catalogo con productos?';
                else
                    echo '¿Esta seguro de desear eliminar este catalogo?';
                ?>
                <form method="post">

                    <br/><br/>
                    <input type="radio" name="delete" value="1"/> SI
                    <input type="radio" name="delete" value="0"/> NO 
                    <br/><br/>
                    <input class="btn-default sin-padding pers-btn icono-aceptar tam-normal" type="submit" value=""/>
                </form>
    <?php
} else if ($view->isFather == true) {
    echo '<div class="alert alert-error">';
    echo 'El c&aacute;talogo no puede ser eliminado ya que tiene asignado otros c&aacute;talogos.';
    echo '</div>';
}
?>
        </div>
        <!--</div>-->
    </div>
</div>