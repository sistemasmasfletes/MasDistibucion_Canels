<div class="panel panel-primary">
    <div class="panel-heading">Rutas asignadas</div>
    <table class="table table-bordered table-condensed table-striped uppercase">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($view->routes as $route): ?>
                <tr>
                    <td><?php echo $route->getCode(); ?></td>
                    <td><?php echo $route->getName(); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

