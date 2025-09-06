<?php
$view->getJsManager()->addJs('application/OperationController/Dashboard/vehicles.js');
?>
<div class="panel panel-primary">
    <div class="panel-heading">Vehículos asignados</div>
    <table class="table table-bordered table-condensed table-striped uppercase">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Volumen</th>
                <th><abbr title="Número económico">Núm. eco.</abbr></th>
                <th>Capacidad</th>
                <th>Marca</th>
                <th>Placas</th>
                <th>Color</th>
                <th>G.P.S.</th>
                <th>Modelo</th>
                <th>Medidas</th>
                <th>Conductor</th>
                <th>Asignar</th>
                <th>Evaluar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($view->vehicles as $vehicle): ?>
                <tr>
                    <td><?php echo $vehicle->getName(); ?></td>
                    <td><?php echo $vehicle->getType(); ?></td>
                    <td><?php echo $vehicle->getFormatVolume(); ?></td>
                    <td><?php echo $vehicle->getEconomicNumber(); ?></td>
                    <td><?php echo $vehicle->getFormatCapacity(); ?></td>
                    <td><?php echo $vehicle->getTrademark(); ?></td>
                    <td><?php echo $vehicle->getPlate(); ?></td>
                    <td><?php echo $vehicle->getColor(); ?></td>
                    <td><?php echo $vehicle->getGps(); ?></td>
                    <td><?php echo $vehicle->getModel(); ?></td>
                    <td>
                        <span title="ancho"><?php echo $vehicle->getFormatWidth(); ?></span> |
                        <span title="alto"><?php echo $vehicle->getFormatHeight(); ?></span> |
                        <span title="profundidad"><?php echo $vehicle->getFormatDeep(); ?></span>
                    </td>
                    <td><?php echo $vehicle->getNameDriver(); ?></td>
                    <td>
                        <a class="btn btn-primary" href="<?php
                        echo $view->url(array('action' => 'assignVehicle',
                            'id' => $vehicle->getId()));
                        ?>" title="Asignar conductor">
                            Asignar
                        </a>
                    </td>
                    <td>
                        <button type="button" class="btn btn-primary eval" user="<?php echo $vehicle->getId(); ?>" name="<?php echo $vehicle->getName(); ?>">
                            Evaluar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="evaluacion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Evaluación</h4>
            </div>
            <div class="modal-body">
                <form role="form">
                    <p>
                        Esta evaluación del vehículo <strong id="nombreUsuario"></strong> es por
                    </p>
                    <div class="radio">
                        <label>
                            <input type="radio" name="tipo" value="1">
                            Mantenimiento preventivo
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="tipo" value="2" id="neutral"checked>
                            Falla mecánica
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="tipo" value="2">
                            Multa de tránsito
                        </label>
                    </div>
                    <div class="form-group">
                        <p>
                            Descripción de la observación:
                        </p>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn btn-primary">
                    Agregar evaluación
                </button>
            </div>
        </div>
    </div>
</div>


