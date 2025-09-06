<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>
                    Reporte Secuencial de Actividades
                </h1>
                <div class="con">
                    <span>Ruta:</span>
                    <select id="route">
                        <!--<option value="0" >Todas</option>-->
                        <?php 
                        foreach($view->routes as $route){
                            echo '<option value="'.$route->getId().'">'.$route->getName().'</option>';
                        }
                        ?>
                    </select>
                    <span>Fecha de ruta:</span>
                    <input type="text" class="dateInput" id="start" readonly="readponly" value="<?php echo $view->startDate->format('Y-m-d');?>">
                    <span>Hora de Salida:</span>
                    <select id="hourExit">
                        <!--<option value="0" >Todas</option>-->
                        <?php 
                        foreach($view->routesHour as $routeHour){
                            echo '<option value="'.$routeHour['routeDate']->format('Y-m-d H:i').'">'.$routeHour['routeDate']->format('Y-m-d H:i').'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div id="reportLoader"><?php echo $view->ManagerImages()->img('images/loading.gif');?></div>
                <div id="reportResult">
                </div>
            </div>
        </div>
    </div>
</div>