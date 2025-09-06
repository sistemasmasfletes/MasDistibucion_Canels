<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Estados de la Compra de Creditos</h1>
                <?php
               echo '<a class="btn" href="' . $view->url(array('action' => 'add')) . '" >Insertar Estado</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
               $view->sc->generatetable();
                ?>
            </div>
        </div>
    </div>
</div>