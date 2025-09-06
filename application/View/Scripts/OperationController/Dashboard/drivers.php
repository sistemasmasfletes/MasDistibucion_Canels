<div class="panel panel-primary">
    <div class="panel-heading">Conductores asignados</div>
    <table class="table table-bordered table-condensed table-striped uppercase">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($view->drivers as $driver): ?>
                <tr>
                    <td><?php echo $driver->getCode(); ?></td>
                    <td><?php echo $driver->getFullName(); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

