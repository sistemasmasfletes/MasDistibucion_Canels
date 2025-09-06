<h1>Puntos de venta</h1>
<?php
echo '<a href="' . $view->url(array('action' => 'add')) . '" >Agregar punto</a>';
$view->sc->generatetable();