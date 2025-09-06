<?php 
foreach ($view->invoicesUsers as $iU)
{
    if($iU->getClient()->getId() == $view->userId)
        $rest = true;
    else
        $rest = false;
}
if ($view->invoicesUsers != null && $rest)
{ ?>
    <table class="table table-striped table-bordered table-condensed" >
        <tr>
            <th>Folio</th>
            <th>Nombre del Cliente</th>
            <th>Numero de Pedidos</th>
            <th>Fecha de Facturacion</th>
            <th>Fecha de Corte</th>
            <th>Estado de la Factura</th>
            <th>Precio Total</th>
        </tr><?php
    foreach ($view->invoicesUsers as $invocesUser)
    {
        if($invocesUser->getClient()->getId() == $view->userId)
        {
        echo '<tr>';
        echo '<td>';
        echo $invocesUser->getId();
        echo '</td>';
        echo '<td>';
        echo $invocesUser->getClient()->getFullName();
        echo '</td>';
        echo '<td>';
        echo $invocesUser->getNumOrders();
        echo '</td>';
        echo '<td>';
        echo date_format($invocesUser->getGeneratedInvoice(),'d-m-Y H:i:s');
        echo '</td>';
        echo '<td>';
        echo date_format($invocesUser->getCutDate(),'d-m-Y H:i:s');
        echo '</td>';
        echo '<td>';
        echo $invocesUser->getStatus() == 0 ? 'Sin pagar' : 'Pagado' ;
        echo '</td>';
        echo '<td>';
        echo $invocesUser->getPriceTotal();
        echo '</td>';
        echo '</tr>';
        }
    }
    ?>
    </table>
    <?php
}
else
{
    echo '<div class="alert alert-error">';
    echo 'No hay reporte para este lapso de tiempo';
    echo '</div>';
}