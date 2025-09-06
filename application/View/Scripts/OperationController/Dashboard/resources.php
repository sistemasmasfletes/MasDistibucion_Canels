<div class="panel panel-primary">
    <div class="panel-heading">Recursos asignados</div>
    <div class="panel-body">
        <div class="col-lg-4">
            <table class="table table-bordered table-condensed table-striped">
                <thead>
                    <tr>
                        <th>
                            <a href="<?php echo $view->url(array('action' => 'drivers')); ?>">
                                Conductores
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($view->drivers as $driver): ?>
                        <tr>
                            <td><?php echo $driver->getFullName(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-lg-4">
            <table class="table table-bordered table-condensed table-striped">
                <thead>
                    <tr>
                        <th>
                            <a href="<?php echo $view->url(array('action' => 'vehicles')); ?>">
                                Vehículos
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($view->vehicles as $vehicle): ?>
                        <tr>
                            <td><?php echo $vehicle->getName(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-lg-4">
            <table class="table table-bordered table-condensed table-striped">
                <thead>
                    <tr>
                        <th>
                            <a href="<?php echo $view->url(array('action' => 'routes')); ?>">
                                Rutas
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($view->routes as $route): ?>
                        <tr>
                            <td><?php echo $route->getNameWithCode(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

