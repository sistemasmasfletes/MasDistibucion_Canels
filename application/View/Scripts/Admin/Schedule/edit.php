<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Programación de rutas</h1>
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'route', 'id' => $view->route->getId())) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if ($view->result == true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Registro modificado con exito';
                    echo '</div>';
                }
                ?>
                <form method="post" >
                    <label>Fecha (yyyy-mm-dd)</label>
                    <input type="text" name="date" id="dateRoot" value="<?php echo $view->scheduledate->getStartDate()->Format('Y-m-d')?>"/>
                    <label>Hora</label>
                    <select class="span1" name="hours" >
                        <?php
                        $cero = '0';
                        for ($i = 0; $i < 24; $i++)
                        {
                            $selected ='';
                            if (strcmp($i ,(int)$view->scheduledate->getStartDate()->Format('H')) == 0)
                                $selected = 'selected = "selected"';
                            if ($i > 9)
                                $cero = '';
                            echo '<option value="'.$cero.$i.'" '.$selected.'>' . $cero . $i .'</option>';
                        }
                        ?>
                    </select>
                    :
                    <select class="span1" name="mins" >
                    <?php
                        $ceroMin = '0';
                        for ($i = 0; $i < 55; $i+=5)
                        {
                            $selectedMin ='';
                            if (strcmp($i ,$view->scheduledate->getStartDate()->Format('i')) == 0)
                                $selectedMin = 'selected = "selected"';
                            if ($i > 9)
                                $ceroMin = '';
                            echo '<option value="'.$ceroMin.$i.'" '.$selectedMin.'>' . $ceroMin . $i .'</option>';
                        }
                        ?>
                    </select>
                    <br/>
                    <button type="submit" class="btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
