<div class="container">
    <div class="row">
        <div id="message"></div>
    </div>
    <div class="row">
        <!--<div class="span12 blockGray">-->
            <h1>Mantenimiento a rutas periodicas</h1>
            <div class="clear">&nbsp;</div>
            <div>
                <h4>Fecha añadida: <?php echo $view->dateAdded;?></h4>
                <h4>Numero de nuevas rutas: <?php echo $view->newSchedules;?></h4>
            </div>
            <div class="clear">&nbsp;</div>
            <div>
                <table class="table table-striped table-bordered table-condensed">
                    <tr>
                        <th>
                            Nombre ruta
                        </th>
                        <th>
                            Fecha
                        </th>
                    </tr>
                    <?php foreach($view->routesNews as $schedule):?>
                    <tr>
                        <td>
                            <?php  echo $schedule['route'];?>
                        </td>
                        <td>
                            <?php echo '<code>'.$schedule['date'].'</code>';?>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </table>
            </div>
        <!--</div>-->
    </div>
</div>
