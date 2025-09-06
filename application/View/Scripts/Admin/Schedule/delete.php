<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'route', 'id' => $view->route)) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                echo '<div class="alert alert-success">';
                echo 'La salida de ruta se ha eliminado';
                echo '</div>';?>
            </div>
        </div>
    </div>
</div>