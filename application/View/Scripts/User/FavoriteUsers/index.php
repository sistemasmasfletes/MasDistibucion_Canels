<!--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" type="text/javascript"></script>-->

<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('table.display').DataTable({
        dom: 'Bfrtip',
        pagingType: "full_numbers",
        ordering: true,
        info:false,
        searching: false,
    });
} );
</script>
<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Tiendas</li>
        <li class="active actualpg ">Tiendas Favoritas</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
        <div class="blockInner">
            <h1>Acceso r&aacute;pido a cat&aacute;logos</h1>

            <?php
            $em = $view->_em;
            
            echo '<div id=messages> </div>';
            echo '<div class="clear" style="height:15px;" ></div>';
            //echo '<h2>Historial de productos comprados</h2>';
            echo '<br></br>';
            echo '<br></br>';
            if (is_array($view->favoriteUsersSellers) && count($view->favoriteUsersSellers) > 0) {
            	
            	echo '<div class="contable">';
            	echo '<table id="favoriteUsersSellers"  class="table table-striped table-bordered table-condensed display tableresp" cellspacing="0" width="100%" >';
                echo '<thead>';
                echo '<tr>';
                echo '<th></th>';
                echo '<th>Proveedor</th>';
                //echo '<th> </th>';
                echo '<th>Productos</th>';
                //echo '<th>Fecha ultimo pedido</th>';
                //echo '<th>Producto</th>';
                //echo '<th> </th>';
                //echo '<th> </th>';
                echo '</tr>';
                echo '</thead>';
                foreach ($view->favoriteUsersSellers as $favoriteUserSeller) {
                    
                    $seller = $favoriteUserSeller->getFavoriteClient();
                    $buyer = $favoriteUserSeller->getClient();
                    $producto = $favoriteUserSeller->getProducto();
                    $ordersRepository = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
                    $catalog = $em->getRepository('DefaultDb_Entities_Catalog')->findBy(array('client' => $seller->getId()));
                    
                    if($producto != NULL){
                        $order  = $ordersRepository->getFavoriteOrder($producto->getId(),$buyer->getId(),$seller->getId());
                    }else{
                         $order  = $ordersRepository->getFavoriteOrder(null,$buyer->getId(),$seller->getId());
                    }
                    echo '<tr>';
                    echo '<td><a href="' . $view->url(array('action' => 'delete', 'id' => $favoriteUserSeller->getId())) . '" <span class="pers-btn icono-tacha icono-tam-tabla"></span>&nbsp;</a></td>';
                    echo '<td>' ;
                    echo ($favoriteUserSeller->getFavoriteClient()->getCommercialName())?$favoriteUserSeller->getFavoriteClient()->getCommercialName():$favoriteUserSeller->getFavoriteClient()->getFirstName();
                    echo '</td>';
                    if(count($catalog) > 0){
                        //echo '<td><a href="' . $view->getBaseUrl() . '/User/Store/viewCatalog/id/' . $favoriteUserSeller->getProducto()->getCatalog()->getId() . '/">Ir al catÃ¡logo del proveedor</a> </td>';
                        //echo '<td><a href="' . $view->getBaseUrl() . '/User/Store/user/id/' . $seller->getId() . '/">Ir al cat&aacute;logo del proveedor</a> </td>';
                        echo '<td><a href="' . $view->getBaseUrl() . '/User/Store/viewAllProducts/id/' . $seller->getId() . '/">Ir al cat&aacute;logo del proveedor</a> </td>';
                        //echo '<td><a  onclick="addFavoriteToCar('.$favoriteUserSeller->getProducto()->getId().',\''.$view->url(array('controller' => 'Store', 'action' => 'viewCart')) .'\')">Realizar nuevo pedido</a> </td>';
                        
                    }else{
                        //echo '<td>&nbsp;</td>';
                        echo '<td>&nbsp;</td>';
                    }
                    /*echo '<td>';
                    if(isset($order[0]["fecha"])){
                    	echo  (isset($order[0]["fecha"]))?$order[0]["fecha"]:'';
                    }
                    echo '</td>';
                    if($producto != NULL){
                        echo '<td>' . $favoriteUserSeller->getProducto()->getName() . '</td>';
                    }else{
                        echo '<td>Sin productos</td>';
                    }*/
                    /*if($producto != null){
                    	//echo '<td><a href="' . $view->getBaseUrl() . '/User/Store/viewCatalog/id/' . $favoriteUserSeller->getProducto()->getCatalog()->getId() . '/">Ir al catÃ¡logo del proveedor</a> </td>';
                    	//echo '<td><a href="' . $view->getBaseUrl() . '/User/Store/user/id/' . $seller->getId() . '/">Ir al catálogo del proveedor</a> </td>';
                    	echo '<td><a  onclick="addFavoriteToCar('.$favoriteUserSeller->getProducto()->getId().',\''.$view->url(array('controller' => 'Store', 'action' => 'viewCart')) .'\')">Realizar nuevo pedido</a> </td>';
                    
                    }else{
                    	echo '<td>&nbsp;</td>';
                    }*/
     				/*echo '<td>';               
                    echo (isset($order[0]["orderId"]))?'<a href="'. $view->url(array('controller' => 'BackStore', 'action' => 'viewOrderShopping', 'id' => $order[0]["orderId"])). '">Ir al pedido</a>':'';
                    echo '</td>';*/
                    echo '</tr>';
                }
                echo '</table>';
                echo '</div>';
            } else {
                echo '<div class="alert alert-block">';
                echo 'Aun no tiene Proveedores como Favoritos';
                echo '</div>';
            }

            /*echo '<div class="clear" style="height:15px;" ></div>';
            //echo '<h2>Historial de productos vendidos</h2>';
            if (is_array($view->favoriteUsersBuyers) && count($view->favoriteUsersBuyers) > 0) {
                echo '<table id="favoriteUsersBuyers" class="table table-striped table-bordered table-condensed display" >';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Producto</th>';
                echo '<th>Cliente</th>';
                echo '<th>Fecha Pedido</th>';
                //echo '<th></th>';
                echo '<th> </th>';
                echo '</tr>';
                echo '</thead>';
                foreach ($view->favoriteUsersBuyers as $invocesUser) {
                    
                    $buyer = $invocesUser->getFavoriteClient();
                    $seller = $invocesUser->getClient();
                    $producto = $invocesUser->getProducto();
                    $ordersRepository = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
                    if($producto != NULL){
                        $order  = $ordersRepository->getFavoriteOrder($producto->getId(),$buyer->getId(),$seller->getId());
                    }else{
                         $order  = $ordersRepository->getFavoriteOrder(null,$buyer->getId(),$seller->getId());
                    }

                    echo '<tr>';
                    echo '<td>';
                    if($producto != NULL){
                    echo $invocesUser->getProducto()->getName();
                    }  else {
                     echo 'Sin productos';   
                    }
                    
                    echo '</td>';
                    echo '<td>';
                    echo ($invocesUser->getFavoriteClient()->getCommercialName())?$invocesUser->getFavoriteClient()->getCommercialName():$invocesUser->getFavoriteClient()->getFirstName();
                    echo '</td>';
                    echo '<td>';
                    echo (isset($order[0]["fecha"]))?$order[0]["fecha"]:'';
                    echo '</td>';
                    //echo '<td>';
                    //echo  (isset($order[0]["orderId"]))?'<a href="' . $view->url(array('controller' => 'BackStore', 'action' => 'viewOrder', 'id' => $order[0]["orderId"])).'">Ir al pedido</a>':'';
                    //echo '</td>';
                    echo '<td>'; 
                    echo ($producto != NULL)?'<a  href="'.$view->url(array('controller' => 'BackStore', 'action' => 'createOrder','productoId'=>$invocesUser->getProducto()->getId())) .'">Realizar nueva Entrega</a>':''; 
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<div class="alert alert-block">';
                echo 'Aun no tiene Clientes como Favoritos';
                echo '</div>';
            }*/
            ?>
        </div>
        <!--</div>-->
    </div>

</div>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.css">
