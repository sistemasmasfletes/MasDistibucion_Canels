<?php
if ($view->point) :
    foreach ($view->point as $p):
        ?>
        <option value="<?php echo $p->getPoint()->getId(); ?>">
        <?php echo $p->getPoint()->getName();
         echo $descripPoint = ($p->getPoint()->getType() == DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER) ? '(Centro de Intercambio)':'(Punto de Venta)'; ?> 
        </option> 
    <?php
    endforeach;
endif;
?>
