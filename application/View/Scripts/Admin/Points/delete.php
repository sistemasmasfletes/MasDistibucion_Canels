<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                echo '<div class="alert alert-success">';
                echo 'Se ha eliminado el punto con exito';
                echo '</div>';
                $view->sc->generateForm();
                ?>
            </div>
        </div>
    </div>
</div>
