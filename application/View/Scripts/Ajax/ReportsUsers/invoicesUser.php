<?php
if ($view->user instanceof DefaultDb_Entities_User && $view->invoices)
{
    ?>
    <h3>Embalajes </h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="border-bottom:0px !important;">
                    Facturación
                </th>
                
            </tr>
            <tr>
                <th>No. Folio</th>
                <th>Comprador</th>
                <th>Fecha de facturacion</th>
                <th>Fecha de corte </th>
                <th>Numero de Pedidos</th>
                <th>Precio Total</th>
                <th>Estado</th>               
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($view->invoices as $invoice)
            {
                $status = $invoice->getStatus();
                ?>
                <tr>
                    <td><?php echo $invoice->getId(); ?></td>
                    <td><?php echo $invoice->getClient()->getFullName(); ?></td>
                    <td><?php echo $invoice->getGeneratedInvoice()->format('Y-m-d H:i'); ?></td>
                    <td><?php echo $invoice->getCutDate()->format('Y-m-d H:i'); ?></td>
                    <td><?php echo $invoice->getNumOrders(); ?></td>
                    <td><?php echo $invoice->getPriceTotal(); ?></td>
                    <td>
                        <select style="width: 100px;" name="status" id="<?php echo $invoice->getId();?>" class="orderId">
                            <option value="1" <?php echo ($status == DefaultDb_Entities_Invoices::STATUS_PAID ? 'selected = "selected"' : '' );?> >Pagada</option>
                            <option value="0" <?php echo ($status == DefaultDb_Entities_Invoices::STATUS_TOPAY ? 'selected = "selected"' : ''); ?>>Sin Pagar</option>
                        </select>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
else
{?>
    <div>
                    Facturación
    </div>
            <?php
    echo '<div class="alert">No se encontro informacion para este usuario</div>';
}
?>

