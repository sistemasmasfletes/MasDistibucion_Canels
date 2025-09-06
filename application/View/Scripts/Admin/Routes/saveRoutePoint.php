<?php 
    $rp = $view->routePoint;
    /* @var $rp DefaultDb_Entities_RoutePoint */
?>
<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <?php  echo '<h1>Edicion del punto '.$rp->getPoint()->getName().' de la ruta '.$rp->getRoute()->getName().'</h1>';?>
                <div class="alert-success" style="padding: 10px;">
                    El punto de ruta se ha guardado correctamente
                </div>
                <br />
                <a class="btn" href="<?php echo $view->url(array('action' => 'points', 'id' => $rp->getRoute()->getId()) );?>">Regresar a puntos de venta</a>
            </div>
        </div>
    </div>
</div>
