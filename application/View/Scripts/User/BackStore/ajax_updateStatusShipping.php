<?php
    if($view->res)
    {
        echo '<div class="alert alert-success">Ha actualizado el estado de envio.<button class="close" data-dismiss="alert">×</button></div>';
    }
    else
    {
        echo '<div class="alert alert-error">Ha ocurrido un error al actualizar el estado de envio.<button class="close" data-dismiss="alert">×</button></div>';
    }
?>
