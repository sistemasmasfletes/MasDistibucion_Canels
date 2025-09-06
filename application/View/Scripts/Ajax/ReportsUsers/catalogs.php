<?php
$dbs = Model3_Registry::getInstance()->get('databases');
$em = $dbs['DefaultDb'];
$productsAdapter = $em->getRepository('DefaultDb_Entities_Product');

if($view->user instanceof DefaultDb_Entities_User )
{
    ?> <div class="accordion" id="accordion2"> <?php
    foreach($view->catalogs as $invoice)
    {
        $products = $productsAdapter->findBy(array('catalog'=>$invoice));
        ?>
        <div class="accordion-group">
            <div class="accordion-heading" style="background-color: #d5d5d5;color:black;font-weight: bold;">
                <a style="text-decoration:none;color:black;" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $invoice->getId()?>">
                  <?php echo $invoice->getTitle()?>
                </a>
                
              </div>
              <div id="collapse<?php echo $invoice->getId();?>" class="accordion-body collapse" style="height: 0px; ">
                <div class="accordion-inner tab-content">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Estatus</th>
                                <th>Ver Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($products as $p)
                            {
                                ?>
                            <tr>
                                <td style="border:  0px !important;"><?php echo $p->getName();?></td>
                                <td style="border:  0px !important;"><?php echo $p->getPrice();?></td>
                                <td style="border:  0px !important;">
                                    <?php $status = $p->getStatus()?>
                                    <div class="btn-group" id="productStatus" pid="<?php echo $p->getId();?>" data-toggle="buttons-radio">
                                        <button class="btn btn-primary<?php echo $status == DefaultDb_Entities_Product::STATUS_ACTIVE?' active':'';?>"
                                                value="<?php echo DefaultDb_Entities_Product::STATUS_ACTIVE;?>">Activo</button>
                                        <button class="btn btn-primary<?php echo ($status == DefaultDb_Entities_Product::STATUS_INACTIVE || $status == DefaultDb_Entities_Product::STATUS_BLOCK )?' active':'';?>"
                                                value="<?php echo DefaultDb_Entities_Product::STATUS_INACTIVE;?>">Inactivo</button>
                                    </div>
                                </td>
                                <td style="border:  0px !important;" class="text-center"><a href="<?php echo $view->url(array('module'=>'User','controller'=>'Store','action'=>'viewProduct','id'=>$p->getId()));?>"
                                       target="_BLANK"><span class=" pers-btn icono-ver-detalle icono-tam-tabla" title="Ver detalle"></span></a>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
              </div>
        </div>
        <?php
    }
    ?> 
    </div>
    <?php
}
else
{
    echo '<div class="alert">No se ha encontrado informacion para este usuario</div>';
}
?>

