<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Sistema</li>
    <li class="active actualpg "><?php  echo '<a  href="' . $view->url(array('action' => 'index')) . '" >Configuración</a>' ?></li>
    <li class="active actualpg ">Editar Configuración</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Edici&oacute;n de Categor&iacute;a</h1>
                <?php
//                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                $view->sc->generateForm();
                ?>
            </div>
        <!--</div>-->
    </div>
</div>