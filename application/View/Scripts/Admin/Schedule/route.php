<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Agenda de rutas</h1>
                <?php echo $view->route->getName(); ?>
                <a class="btn" href="<?php echo $view->url(array('action' => 'add', 'route' => $view->route->getId())); ?>">Agendar esta ruta</a>
                <a class="btn" href="<?php echo $view->url(array('action' => 'addRecurrent', 'route' => $view->route->getId())); ?>">Agendar esta ruta periodicamente</a>
            </div>
        </div>
    </div>
    <div style="height: 15px;" ></div>
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h2>Salidas programadas</h2>                
                <?php
                if (is_array($view->schedules) && count($view->schedules) > 0)
                {
                    echo '<table class="table table-striped table-bordered table-condensed" >';
                    echo '<tr>';
                    echo '<th>Fecha</th>';
                    echo '<th>Editar</th>';
                    echo '<th>Eliminar</th>';
                    echo '</tr>';
                    foreach ($view->schedules as $invocesUser)
                    {
                        echo '<tr>';
                        echo '<td>';
                        echo $invocesUser->getStartDate()->format('d-m-Y H:i:s');
                        if($invocesUser->getScheduleParent()!=null)
                            echo ' <code>Generada por Sistema</code>';
                        echo '</td>';
                        echo '<td>';
                        echo '<a href="' . $view->url(array('action' => 'edit', 'id' => $invocesUser->getId(), 'route' => $view->route->getId())) . '">Editar</a>';
                        echo '</td>';
                        echo '<td>';
                        echo '<a href="' . $view->url(array('action' => 'delete', 'id' => $invocesUser->getId(), 'route' => $view->route->getId())) . '">Eliminar</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
                else
                {
                    echo 'Aun no se programan salidas para esta ruta';
                }
                ?>
            </div>
        </div>
    </div>
    <div style="height: 15px;" ></div>
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h2>Salidas programadas periodicas</h2>                
                <?php
                if (is_array($view->schedulesRecurrents) && count($view->schedulesRecurrents) > 0)
                {
                    echo '<table class="table table-striped table-bordered table-condensed" >';
                    echo '<tr>';
                    echo '<th>Fecha</th>';
                    echo '<th>Editar</th>';
                    echo '<th>Eliminar</th>';
                    echo '</tr>';
                    foreach ($view->schedulesRecurrents as $sr)
                    {
                        echo '<tr>';
                        echo '<td>';
                        echo $sr->getStartDate()->format('d-m-Y H:i:s');
                        echo '</td>';
                        echo '<td>';
                        echo '<a href="' . $view->url(array('action' => 'editRecurrent', 'id' => $sr->getId(), 'route' => $view->route->getId())) . '">Editar</a>';
                        echo '</td>';
                        echo '<td>';
                        echo '<a href="' . $view->url(array('action' => 'delete', 'id' => $sr->getId(), 'route' => $view->route->getId())) . '">Eliminar</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
                else
                {
                    echo 'Aun no se programan salidas para esta ruta';
                }
                ?>
            </div>
        </div>
    </div>
</div>