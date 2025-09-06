<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Edición de Vehículos</h1>
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->result == true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Registro modificado con exito';
                    echo '</div>';
                }
                $view->sc->generateForm();
                ?>
            </div>
        </div>
    </div>
</div>