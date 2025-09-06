<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Rutas</li>
    <li class="active "><?php echo '<a href="' . $view->url(array('action' => 'index')) . '" >Calendario</a>'?></li>
    <li class="active actualpg ">Agregar Calendario</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Alta en Calendario</h1>
                <?php
//                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->result == true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Registro insertado con exito';
                    echo '</div>';
                }
                $view->sc->generateForm();
                ?>
            </div>
        <!--</div>-->
    </div>
</div>