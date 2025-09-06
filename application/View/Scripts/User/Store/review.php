<?php

    if (isset($_SESSION["em"])) {
        $em = $_SESSION["em"];
        $em->
        $_SESSION["em"] = "";
    }

?>

<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Clientes</li>
        <li class="active "> <?php
            echo '<a href="'.$view->linkTo("/User/Store/viewCart/").'" >Carrito</a>';
            ?></li>
        <li class="active actualpg ">Generar Pedido</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">                
                <?php
                $view->helper('ECommerce')->cartDetails($view->showPayPalButton, $view->modeDeliveryService, $view->tiendaSelected, $view->pagoContraEntrega, $view->action);
                ?>
                <form action="<?php echo $view->url(array('module' => 'User', 'controller' => 'Store', 'action' => 'confirmOrder')); ?>" method="post">
                    <?php if ($view->viewUser instanceof DefaultDb_Entities_User) : ?>
                        <h3>Autorizaci&oacute;n del Cliente</h3>
                        <label for="password">Cliente: <?php echo $view->viewUser->getCommercialName(); ?></label>
                        Contrase&ntilde;a: 
                        <input type="password" name="pass"/>
                        <div class="clear"></div>
                    <?php endif;
                    if ($view->tiendaSelected):
                        ?>
                        <input name="store" type="hidden" value=" <?php echo $view->tiendaSelected->getId() ?>"/>
                    <?php endif; ?>
                    <?php $view->helper('ECommerce')->printPagoOrden();  ?>
                    <div id="shippingDateInformation"></div>
                    <input type="hidden" name="shippingDate" id="shippingDate" value="">
                    <div class="clear" style="margin-top: 15px;"></div>
                    <br/>
                    <input type="hidden" id="comments" name="comments" value="<?php echo $view->comments ?>"/>
                    <input class="btn-default btn-generar-pedido" type="submit" value="Generar pedido"/>
                    <br />
                </form>
            </div>
        <!--</div>-->
    </div>
</div>