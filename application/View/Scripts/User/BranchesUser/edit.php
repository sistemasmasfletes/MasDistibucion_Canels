<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Panel</li>
        <li class="active "> <?php
            echo '<a href="' . $view->url(array('action' => 'index')) . '" >Sucursales</a>';
            ?></li>
        <li class="active actualpg ">Editar Sucursal</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Edición de mis sucursales</h1>
                <?php
//                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
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
        <!--</div>-->
    </div>
</div>