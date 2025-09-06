<?php 
//echo '<option value="0">Todas</option>';
if($view->result==true){
    foreach ($view->routesHours as $date) {
        echo '<option value="'.$date['routeDate']->format('Y-m-d H:i').'">'.$date['routeDate']->format('Y-m-d H:i').'</option>';
    }
}

