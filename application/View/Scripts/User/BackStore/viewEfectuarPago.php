<?php
    $order =  $view->order;
    $packs = $view->packs;
    $tipo = $view->tipo;
    $pago = $view->pago;
?>
<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Panel</li>
    <li class="active "> <?php
        $credentials = Model3_Auth::getCredentials();
        $action = ($view->order->getBuyer()->getId() == $credentials['id']) ? 'viewOrderShopping' : 'viewOrder';
        if($action == 'viewOrderShopping' ){
            echo '<a href="' . $view->getBaseUrlPublic() ."/User/BackStore/shopping" .'" >Recolecciones</a>';
        } else {
            echo '<a href="' . $view->getBaseUrlPublic() ."/User/BackStore/orders" .'" >Entregas</a>';    
        }

        ?></li>
    <li class="active "> <?php
        $credentials = Model3_Auth::getCredentials();
        $action = ($view->order->getBuyer()->getId() == $credentials['id']) ? 'viewOrderShopping' : 'viewOrder';
        echo '<a href="' . $view->url(array('action' => $action),true) . '" >Orden</a>';
        ?></li>
    <li class="active actualpg ">Efectuar Pago</li>
</ol>
</div>

<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <?php
                    if(isset($_SESSION["intIdTipoPagoCar"])){
                        $pago = $_SESSION["intIdTipoPagoCar"];
                        $_SESSION["intIdTipoPagoCar"] = "";
                    }
                    $view->EfectuarPago()->printOrder($order, $tipo, $pago);
                ?>
            </div>
        <!--</div>-->
    </div>
</div>

<style>
    input ,select{ display: block; }
</style>