<div class="contiene-bread">

    <ol class="breadcrumb">

        <li class="active">Clientes</li>

        <li class="active actualpg ">Carrito</li>

    </ol>

</div>

<div class="container">

    <div class="row" >

        <!--<div class="span12 blockGray">-->
        <?php
        if($view->tiendaSelected){
        	/*echo '<a class="btn-default sin-padding " href="'.$view->url(array('module'=>'User','controller'=>'Store','action'=>'user','id'=>$view->tiendaSelected->getId())).'" title="Volver a tienda">';
        	 echo '<span style="float:right;background-color:#08A4DA;width:30%;border-radius:5%;text-align:center;color:#fff;padding:.5em; box-shadow:-19px 12px 17px -7px rgba(13,12,12,0.58);"> << TIENDA : '.$view->tiendaSelected->getCommercialName().'</span></a>';*/
        	 
        	echo '<a class="btn-default sin-padding " href="'.$view->url(array('module'=>'User','controller'=>'Store','action'=>'viewAllProducts','id'=>$view->tiendaSelected->getId())).'" title="Volver a tienda">';
        	echo '<span style="float:right;background-color:#08A4DA;width:30%;border-radius:5%;text-align:center;color:#fff;padding:.5em; box-shadow:-19px 12px 17px -7px rgba(13,12,12,0.58);"> << TIENDA : '.$view->tiendaSelected->getCommercialName().'</span></a>';
        ?>
            <div class="blockInner">                

                <?php

                $view->helper('ECommerce')->cartDetails($view->showPayPalButton,$view->modeDeliveryService, $view->tiendaSelected, $view->pagoContraEntrega, $view->action);

                ?>

            </div>
		<?php } ?>
        <!--</div>-->

    </div>

</div>

