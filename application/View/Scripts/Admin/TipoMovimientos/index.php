<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Tipo de Movimiento</h1>
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'add')) . '" >Agregar Tipo de Movimiento</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                $view->sc->generatetable();
                ?>
            </div>
        </div>
    </div>
</div>
