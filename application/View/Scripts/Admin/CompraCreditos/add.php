<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Compra de Creditos</h1>
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'administracionLogCliente')) . '" >Administracion de Depositos</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->result == true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Compra Realizada con exito';
                    echo '</div>';
                }
                $view->sc->generateForm();
                ?>
            </div>
        </div>
    </div>
</div>