<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Puntos de venta > <span>Ruta: <?php echo $view->route->getName(); ?></span></h1>
                <a class="btn" href="<?php echo $view->url(array('action' => 'index'), true);?>">Regresar a Rutas</a>
                <a class="btn" href="<?php echo $view->url(array('action' => 'newPoint'), true); ?>" >Agregar nuevo punto de venta</a>
                <br />
                <br />
                <?php if(is_array($view->routePoints) == true && count($view->routePoints) > 0): ?>
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>Clave</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Subir</th>
                        <th>Bajar</th>
                        <th>Eliminar</th>
                        <th>Editar</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach($view->routePoints as $rp): ?>
                        <tr>
                            <td><?php echo '[' . $rp['idPoint'] . '] ' . $rp['code'];?></td>
                            <td><?php echo $rp['name'];?></td>
                            <td>
                                <?php 
                                    if($rp['type'] == DefaultDb_Entities_Point::TYPE_SALE_POINT)
                                    {
                                        echo 'Punto de Venta';
                                    }
                                    else
                                    {
                                        echo 'Centro de Intercambio';
                                    }
                                ?>
                            </td>
                            <td><a href="<?php echo $view->url(array('action' => 'stepUpRoutePoint', 'routePointId' => $rp['id']), true);?>">Subir</a></td>
                            <td><a href="<?php echo $view->url(array('action' => 'stepDownRoutePoint', 'routePointId' => $rp['id']), true);?>">Bajar</a></td>
                            <td><a href="<?php echo $view->url(array('action' => 'deleteRoutePoint', 'routePointId' => $rp['id']), true);?>">Eliminar</a></td>
                            <td><a href="<?php echo $view->url(array('action' => 'editRoutePoint', 'routePointId' => $rp['id']), true);?>">Editar</a></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="alert">
                        La ruta todavia no tiene puntos
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>