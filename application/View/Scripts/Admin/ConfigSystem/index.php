<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Sistema</li>
    <li class="active actualpg ">Configuración</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Configuraci&oacute;n del sistema</h1>
                <?php
                echo '<div class="clear" style="height:15px;" ></div>';
                $view->sc->generatetable();
                ?>
            </div>
            <div class="blockInner">
                <h1>Mantenimiento</h1>
                <a class="btn btn-default sin-padding" href="<?php echo $view->url(array('controller'=>'Mantenimiento','action'=>'schedules','module'=>'Admin'));?>"><span class="pers-btn icono-ejecutar-mantenimiento-manual tam-normal"></span></a>
            </div>
        <!--</div>-->
    </div>
</div>
