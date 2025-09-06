<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Agenda de rutas</h1>
                <form method="post">
                    <div style="float:left; padding-right: 20px;">
                        <label class="control-label">Fecha (yyyy-mm-dd)</label>
                        <input type="text" name="date"id="date" />
                    </div>
                    <div>
                        <label>Fecha (yyyy-mm-dd)</label>
                        <input type="text" name="dateSecond" id="dateSecond" />
                    </div>
                    <br/>
                    <button type="submit" class="btn">Consultar</button>
                </form>
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
                    echo '<th>Ruta</th>';
                    echo '<th>Estado</th>';
                    echo '<th>Asignar</th>';
                    echo '</tr>';
                    foreach ($view->schedules as $favoriteUserBuyer)
                    {
                        echo '<tr>';
                        echo '<td>';
                        echo $favoriteUserBuyer['start_date'];
                        echo '</td>';
                        echo '<td>';
                        echo $favoriteUserBuyer['name'];
                        echo '</td>';                        
                        echo '<td>';
                        if($favoriteUserBuyer['user_id'] == null)
                        {
                            echo 'Sin asignar';
                        }
                        else
                        {
                            echo 'Asignada';
                        }
                        echo '</td>';
                        echo '<td>';
                        echo '<a href="' . $view->url(array('action' => 'asign', 'id' => $favoriteUserBuyer['id'])) . '">Asignar</a>';
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