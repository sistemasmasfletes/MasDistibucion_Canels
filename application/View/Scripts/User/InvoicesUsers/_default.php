<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Panel</li>
    <li class="active actualpg ">Facturación</li>
</ol>
</div>
<div class="container" id="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Facturas</h1>
                <?php
                echo 'Fecha de Inicio: <input type="text" value="" name="startDateUser" id="startDateUser" />&nbsp; &nbsp;';
                echo 'Fecha de Fin: <input type="text" value="" name="endDateUser" id="endDateUser" /> &nbsp; &nbsp;';
                echo '<a class="btn-default sin-padding" href="#" id="findUser" name="findUser"> <span class="pers-btn icono-obtener-facturas tam-normal"></span></a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                ?>
                <div style="height: 15px;" ></div>
                <div id="respuesta">
                    No hay reporte para este lapso de tiempo
                </div>
            </div>
        <!--</div>-->
    </div>

</div>