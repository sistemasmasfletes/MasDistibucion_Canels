<?php if ($view->vehicle !== null) : ?>
    <div class="alert alert-info">
        <p>
            Actualmente este vehículo esta asignado a <strong><?php echo $view->vehicle->getDriver()->getFullNameWithType(); ?></strong>.
        </p>
    </div>
<?php endif; ?>
<div class="panel panel-primary">
    <div class="panel-heading">Vehículo por asignar</div>
    <table class="table table-bordered table-condensed table-striped uppercase">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Volumen</th>
                <th>Número económico</th>
                <th>Capacidad</th>
                <th>Marca</th>
                <th>Placas</th>
                <th>Color</th>
                <th>G.P.S.</th>
                <th>Modelo</th>
                <th>Ancho</th>
                <th>Altura</th>
                <th>Prof.</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $view->vehicle->getName(); ?></td>
                <td><?php echo $view->vehicle->getStrType(); ?></td>
                <td><?php echo $view->vehicle->getFormatVolume(); ?></td>
                <td><?php echo $view->vehicle->getEconomicNumber(); ?></td>
                <td><?php echo $view->vehicle->getFormatCapacity(); ?></td>
                <td><?php echo $view->vehicle->getTradeMark(); ?></td>
                <td><?php echo $view->vehicle->getPlate(); ?></td>
                <td><?php echo $view->vehicle->getColor(); ?></td>
                <td><?php echo $view->vehicle->getGps(); ?></td>
                <td><?php echo $view->vehicle->getModel(); ?></td>
                <td><?php echo $view->vehicle->getFormatWidth(); ?></td>
                <td><?php echo $view->vehicle->getFormatHeight(); ?></td>
                <td><?php echo $view->vehicle->getFormatDeep(); ?></td>
            </tr>
        </tbody>
    </table>
</div>
<form role="form" method="post" action="#">
    <input type="hidden" name="vehicle" value="<?php echo $view->vehicle->getId(); ?>"/>
    <div class="form-group">
        <label for="driver">Seleccionar el conductor</label>
        <select name="driver" id="driver" class="form-control">
            <?php foreach ($view->drivers as $driver): ?>
                <option value="<?php echo $driver->getId(); ?>">
                    <?php echo $driver->getFullName(); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <button class="btn btn-primary">
            Asignar vehículo al conductor
        </button>
    </div>
</form>