<?php
if($view->user instanceof DefaultDb_Entities_User)
{
    ?>
 
<table class="table table-bordered table-striped">
   
    <thead>
    <tr style="border-bottom: 1px solid #998c73 !important;background-color: #eac3a0 !important;">
        <th style="border: 0px !important">Mis ventas</th>
        <th style="border: 0px !important" ></th>
        <th style="border: 0px !important" ></th>
        <th style="border: 0px !important"></th>
        <th style="border: 0px !important"></th>
    </tr>
        <tr>
            <th>No. Orden</th>
            <th>Comprador</th>
            <th>fecha de compra</th>
            <th>Recoleccion</th>
            <th>Ver detalles</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($view->buyerOrders as $order)
            {
            ?>
            <tr>
                <td style="border:  0px !important;"><?php echo $order->getId();?></td>
                <td style="border:  0px !important;"><?php echo $order->getSeller()->getFullName();?></td>
                <td v><?php echo $order->getCreationDate()->format('Y-m-d H:i');?></td>
                <td style="border:  0px !important;"><?php echo $order->getShippingStatusString();?></td>
                <td style="border:  0px !important;"><a href="#" pid="<?php echo $order->getId();?>" class="  pers-btn icono-ver-detalle icono-tam-tabla openModal" data-toggle="modal"></a></td>
            </tr>
            <?php
            }
        ?>
    </tbody>
</table>
 
 
<table class="table table-bordered table-striped">
    <thead>
        <tr style="border-bottom: 1px solid #998c73 !important;background-color: #eac3a0 !important;">
        <th style="border: 0px !important; ">Mis compras</th>
        <th style="border: 0px !important" ></th>
        <th style="border: 0px !important" ></th>
        <th style="border: 0px !important"></th>
        <th style="border: 0px !important"></th>
    </tr>
        <tr>
            <th>No. Orden</th>
            <th>Vendedor</th>
            <th>fecha de compra</th>
            <th>Recoleccion</th>
            <th>ver detalles</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($view->sellerOrders as $order)
            {
            ?>
            <tr>
                <td style="border:  0px !important;"><?php echo $order->getId();?></td>
                <td style="border:  0px !important;"><?php echo $order->getBuyer()->getFullName();?></td>
                <td style="border:  0px !important;"><?php echo $order->getCreationDate()->format('Y-m-d H:i');?></td>
                <td style="border:  0px !important;"><?php echo $order->getShippingStatusString();?></td>
                <td style="border:  0px !important;"><a href="#" pid="<?php echo $order->getId();?>" class="  pers-btn icono-ver-detalle icono-tam-tabla openModal" data-toggle="modal"></a></td>
            </tr>
            <?php
            }
        ?>
    </tbody>
</table>

    <?php
}
else
{
    echo '<div class="alert">No se encontro informacion para este usuario</div>';
}
?>


 