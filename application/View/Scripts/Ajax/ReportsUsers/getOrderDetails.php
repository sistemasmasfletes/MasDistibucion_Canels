<?php
if($view->order instanceof DefaultDb_Entities_M3CommerceOrder)
{
    echo '<div class="span6">';
    $view->Orders()->printOrderRepord( $view->order , 6);
    echo '</div>';
}
else
{
    echo '<div class="alert">No se ha encontrado informacion para esta orden</div>';
}

