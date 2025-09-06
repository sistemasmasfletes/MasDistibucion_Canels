<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Usuarios</h1>
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'add')) . '" >Agregar usuario</a>';
                echo '<a class="btn" href="' . $view->url(array('controller'=>'ReportsUsers','action' => 'index')) . '" >Reportes por usuario</a>';
                echo '<a class="btn" href="' . $view->url(array('controller'=>'ReportsUsers','action' => 'branchesPending')) . '" >Solicitudes de Sucursales</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->users)
                {
                    $view->sc->generatetable($view->users);
                }
                ?>
            </div>
        </div>
    </div>
</div>
