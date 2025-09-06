<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Clientes</li>
        <li class="active "> <?php
            echo '<a href="' . $view->linkTo("/User/Store/viewCart") . '" >Carrito</a>';
            ?></li>
        <li class="active actualpg ">Confirmar Orden</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                
                <?php echo $view->mensaje . "<br />" ;
                if($view->payment_result && $view->error_stock == false) : ?>
                	<div class="alert alert-info" style="width:40%; text-align:center">
                		<span style="font-size:1.5em;font-weight: bold;">&iexcl;Felicidades!<br />
                		Tu pedido se Envi&oacute; con &eacute;xito, &iexcl;Gracias!</span><br /><br />
                		<!--span style="font-size:1.5em;">Puedes ver el estatus de tu entrega <a href="<?php //echo $view->getBaseUrl('/User/BackStore/shopping')?>">Aqu&iacute;</a></span--> 
                		<span style="font-size:1em; color:#000;"><?php echo $view->sellerdat; ?></span><br /><br />
	               	</div>
                <?php elseif($view->error_stock == true) : ?>
                    Error al generar el pedido, no se cuenta con suficiente producto.
                <?php else : ?>
                    <!--La contrase&ntilde;a es incorrecta.-->
                <?php endif; ?>
            </div>
        <!--</div>-->
    </div>
</div>
