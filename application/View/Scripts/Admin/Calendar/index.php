<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Rutas</li>
    <li class="active actualpg ">Calendario</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Calendario</h1>
                <?php
                echo '<a class="btn btn-default sin-padding" href="' . $view->url(array('action' => 'add')) . '" ><span class="pers-btn icono-agregar-evento tam-normal"></span></a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                $view->sc->generatetable();
                ?>
            </div>
        <!--</div>-->
    </div>
</div>
