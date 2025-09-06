<?php
use Doctrine\ORM\NoResultException;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author H4845
 */
class View_Helper_Orders extends Model3_View_Helper {
	
	public $imgUrl =  array();
	

    public function printOrder($order, $isBuyer) {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        $packagesAdapter = $em->getRepository('DefaultDb_Entities_PackageToOrder');
        $pagosAdapter = $em->getRepository('DefaultDb_Entities_Pagos');
        $pagos = $pagosAdapter->findBy(array('compraVenta' => $order));
        $packs = $packagesAdapter->findBy(array('order' => $order));

        $vendedor = $order->getSeller();
        $comprador = $order->getBuyer();
        
        ?>
        <style>
            input ,select{ display: block; }
        </style>
        <h1>Detalles de la Orden</h1>
        <?php
        $action = $isBuyer ? 'orders' : 'shopping';
//                echo '<a class="btn" href="' . $this->_view->url(array('action' => $action)) . '" >Regresar</a>';
        echo '<div class="clear" style="height:15px;" ></div>';
        ?>
        <input type="hidden" id="txtBaseUrl" name="txtBaseUrl" value="<?php echo $this->_view->getBaseUrl() ?>" disabled="true">
        <input type="hidden" id="txtIdOrden" name="txtIdOrden" value="<?php echo $order->getId() ?>" >
        <div class="row">

            <div style="width:100%; box-sizing:border-box; margin-bottom:2em; display: flex; justify-content: center; align-items: center;">
            
        	<?php 
        		$this->printOrderDates($order, $isBuyer);
        		$user = $isBuyer ? $comprador : $vendedor;
        		$this->printUserDates($user, $isBuyer, null, $order);
           	?>
			</div>
			
			<div class="">
			<?php 
				echo '<b style="margin-right:2em;">Contenido del paquete: </b>'.$order->getContent().'<br />';
				echo '<b style="margin-right:2em;">Contacto de recolecci&oacute;n: </b>'.$order->getContactS().'<br />'; 
				echo '<b style="margin-right:2em;">Contacto de entrega: </b>'.$order->getContactR().'<br />';
			?>
			</div><br />
           	
            <?php //if (count($order->getProducts())): //esto se comenta para poder agregar productos desde el vendedor ?>
                <div class="row">
                    <?php $this->printProductsDates($order,$isBuyer); ?>
                </div><br />
            <?php //endif;?>
            <div class="row" >
                <?php $this->printComentarios($order); ?>
            </div><br />
            <?php 
            if($order->getShippingStatus() != DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_NOT_SHIPPED){?>
           	
           	<div class="row" >
            	 
            <?php $this->printPointsAc($order);?>
            
			</div><br />            
            <?php } ?>
            
            <div class="row">
                <?php
                if ($isBuyer) {
                    $this->printShippingDates($order, $isBuyer);
                }
                ?>
                <?php
                if (!$isBuyer && count($pagos)==0/* && count($order->getPackages()) == 0 */) {
                    $this->printTipoPago($order);
                }
                ?>
            </div><br />
            <?php 
            if(count($this->imgUrl) > 0 ){
            ?>
            <div class="row">
            
            	<div id="myCarousel" class="carousel slide" data-ride="carousel">
				  <!-- Indicators -->
				  <ol class="carousel-indicators">
            
		            <?php
		            $a = 0;
			            	foreach ($this->imgUrl as $img){
			
								echo '<li data-target="#myCarousel" data-slide-to="'.$a.'"></li>';
								//echo $img['imgUrl'];
								$a++;
							
							}
						
		            ?>
				  </ol>
				
				  <!-- Wrapper for slides -->
				  <div class="carousel-inner" role="listbox">
				  
		            <?php
		            $a = 0;
		            	if(count($this->imgUrl) > 0 ){
			            	foreach ($this->imgUrl as $img){
	
								echo ($a == 0)? '<div class="item active">': '<div class="item">';

								echo '<img src="'.$img['imgUrl'].'" alt=""></div>';
								//echo $img['imgUrl'];
								$a++;
							
							}
						}
		            ?>
				  
				    <!-- div class="item active">
				      <img src="/distribucion0.2.1/public/images/evidencias/4.png" alt="">
				    </div>
				
				    <div class="item">
				      <img src="/distribucion0.2.1/public/images/evidencias/7.png" alt="">
				    </div-->
				
				  </div>
				
				  <!-- Left and right controls -->
				  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
				    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				  </a>
				  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
				    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				  </a>
				</div>      
			</div>  
			
			<?php }?>
            
        </div>
        <div class="row">
            <?php if ($order->getRecurrent() == DefaultDb_Entities_M3CommerceOrder::RECURRENT_ACTIVE): ?>
            <?php $this->printOrderRecurrent($order); ?>
        <?php endif; ?>
        </div><br />
        </div>
        <div class="row">
            <div class="span5">
                <?php
                if (Model3_Auth::getCredentials('type') == DefaultDb_Entities_User::USER_DRIVER) {
                    if ($order->getShippingStatus() == DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED || $order->getShippingStatus() == DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED) {
                        $strBtn = 'Recolectado';
                        if ($order->getShippingStatus() == DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED) {
                            $strBtn = 'Entregado';
                        }

                        echo '<input class="btn" type="button" id="changeStatusShippingBtn" value="' . $strBtn . '"/>';
                        echo '<div id="divResultShipping"></div>';
                    }
                }
                ?>
            </div>

        </div><br />
        <?php
    }

    private function printOrderRecurrent($order) {
        ?>
        <h4>Estado de la Orden</h4>
        <select id="changeStatusOrder">
            <?php $selected = ($order->getOrderStatus() == DefaultDb_Entities_M3CommerceOrder::ORDER_STATUS_ACTIVE) ? "selected = 'selected'" : ''; ?>
            <option value="<?php echo DefaultDb_Entities_M3CommerceOrder::ORDER_STATUS_ACTIVE ?>" <?php echo $selected; ?>>Activa</option>
        <?php $selected = ($order->getOrderStatus() == DefaultDb_Entities_M3CommerceOrder::ORDER_STATUS_INTACTIVE) ? "selected = 'selected'" : ''; ?>                    
            <option value="<?php echo DefaultDb_Entities_M3CommerceOrder::ORDER_STATUS_INTACTIVE ?>"<?php echo $selected; ?>>Inactiva</option>
        </select>
        <?php
    }

    private function printOrderDates($order, $isBuyer) {
        ?>
	    <div style="display:inline-block; width:49%;">
        	<h4>Datos de la orden</h4>
            <?php if ($order->getOrderParent()): ?>
                <h5>Esta orden fue generada de manera recurrente</h5>
        	<?php endif; ?>
	        
            <div  class="row">
	        	<div class=""><span> Folio: </span><?php echo $order->getId(); ?>
	          	</div>

	            <div class=""><span> Fecha compra: </span>
	                <?php         
	                $fechaCompra = $order->getCreationDate();        
	                echo $fechaCompra->format('Y-m-d');
	                ?>
	          	</div>

	            <div class=""><span> Estatus de pago: </span>
	            <?php
	                if ($isBuyer) {
	                    ?>
	                    <?php echo $order->getPaymentStatusString();?>
	                    
	                    <!-- select id="statusPaid" disabled="false">
	                        <option value="<?php //echo DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_NOT_PAID; ?>"
	            				<?php //echo $order->getPaymentStatus() == DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_NOT_PAID ? 'selected="selected"' : ''; ?>>
	                        	<?php //echo $order->getPaymentStatusString(DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_NOT_PAID); ?>
	                        </option>
	                        <option value="<?php //echo DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_PAID ?>"
	            				<?php //echo $order->getPaymentStatus() == DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_PAID ? 'selected="selected"' : ''; ?>>
	                    		<?php //echo $order->getPaymentStatusString(DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_PAID); ?>
	                        </option>
	                    </select-->
	                <?php
	                } else {
	                 echo $order->getPaymentStatusString(); 
	                }?> 
	           	</div> 
	            <div class=""><span> Estatus de envío: </span>
	                <?php echo $order->getShippingStatusString(); ?>
	            </div>
            </div>
     	</div>
    <?php
    }

    private function printOrderDatesTable($order) {
        ?>

        <table class="table table-bordered table-striped">
            <tbody>
                <tr style="border-bottom: 1px solid #998c73 !important;background-color: #eac3a0 !important;">
                	<th style="border: 0px !important">Datos de la orden</th>
                    <th style="border: 0px !important" ></th>
               	</tr>
                <tr>
                    <td>Folio:</td>
                    <td><?php echo $order->getId(); ?></td>
                </tr>
                <tr>
                    <td>Fecha compra:</td>
                    <td><?php echo $order->getPaymentStatusString(); ?></td>
                </tr>
                <tr>
                    <td>Estatus Pago:</td>
                    <td><?php echo $order->getPaymentStatusString(); ?></td>
                </tr>
                <tr>
                    <td>Estatus recolecci&oacute;n:</td>
                    <td><?php echo $order->getShippingStatusString(); ?></td>
                </tr>
            </tbody>

        </table>

        <?php
    }

    private function printShippingDates($order, $isBuyer) {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        $packagesAdapter = $em->getRepository('DefaultDb_Entities_PackageToOrder');
        $packs = $packagesAdapter->findBy(array('order' => $order));

//        if($order->getOrderParent())
//            $order = $order->getOrderParent();
        echo '<br><div class="row"><h4>Informaci&oacute;n costo del Embalaje de productos</h4>';
        switch ($order->getShippingStatus()) {
            /*case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_NOT_SHIPPED:
                $credentials = Model3_Auth::getCredentials();
                if ($credentials['type'] != DefaultDb_Entities_User::USER_ADMIN && $credentials['type'] != DefaultDb_Entities_User::USER_OPERATION_CONTROLLER):
                    ?>
                    <a class=" btn-default sin-padding" href="<?php echo $this->_view->url(array('action' => 'creteShipping'), true); ?>" ><span class="pers-btn icono-generar-pedido tam-normal"></span></a>
                    <?php
                else:
                    ?>
                    <div class="alert">
                        <strong>Aun no se genera el Envio </strong>.
                    </div>
                <?php
                endif;
                if (!$isBuyer):
                    ?>
                    <div class="alert">
                        <strong>Aun no se genera el Envio </strong> por parte del vendedor,
                        si gusta lo podra generar y el costo del envio se le hara efectivo a usted.
                    </div>
                    <?php
                endif;

                break;*/
            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED:
            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED:
			case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_IN_INTERCHANGE_CENTER:
            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_DELIVERED:

                if (is_array($packs) && count($packs) > 0) {
                    ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cantidad</th>
                                <th>Paquetes</th>
                                <th>Precio</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($packs as $p) {
                                ?>
                                <tr>
                                    <td><?php echo $p->getNumPackage(); ?></td>
                                    <td><?php echo $p->getNamePackage() ?></td>
                                    <td>$<?php echo number_format($p->getPrice()/0.182,2,'.',''); ?></td>
                                    <td>$<?php echo number_format($p->getTotalPrice()/0.182,2,'.','');
                        $total += $p->getTotalPrice();
                                ?></td>
                                </tr>
                        <?php
                    }
                    ?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><a target="_blank" href="<?php echo $this->_view->url(array('module' => 'User', 'controller' => 'Dompdf', 'action' => 'index', 'id' => $order->getId())); ?>" class="btn-default sin-padding"><span class="pers-btn icono-generar-etiqueta tam-normal"></span></a></td>
                                <td><b>$<?php echo number_format($total/0.182,2,'.',''); ?></b></td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php
                }
                break;
        }
        echo '</div>';
    }

    private function printUserDates($user = null, $isBuyer, $users = null, $order = null) { 
		echo '<div style="display:inline-block; width:49%;" >';
		
        echo (!$isBuyer) ? '<h4>Datos de Proveedor</h4>' : '<h4>Datos de Cliente</h4>';
        if ($this->_view->isCreateOrder == true) {
			$this->printSearchClients($isBuyer);
        } else {
	        $this->printClient($user, $isBuyer, $users, $order);
        }
        
        if($user !==null && $user->getId() != 24){
        ?>
	        <div class="row">
		        <div class=""><span> Nombre: </span>
		            <?php echo ($user ? '' : ''); ?> <?php echo ($user !== null ? $user->getFirstName() : ''); ?>
		        </div>
		        <div class=""><span> Apellido: </span>
		            <?php echo ($user ? '' : ''); ?> <?php echo ($user !== null ? $user->getLastName() : ''); ?>
		        </div>
		        <div class=""><span> Tel&eacute;fono: </span>
		            <?php echo ($user ? '' : ''); ?> <?php echo ($user !== null ? $user->getLocalNumber() : ''); ?>
		        </div>
		        <div class=""> <span> Cel&uacute;lar: </span>
		            <?php echo ($user ? '' : ''); ?> <?php echo ($user !== null ? $user->getCellPhone() : ''); ?>
		        </div>
	        </div>

        <?php
        }
        
        echo '</div>';
    }

    private function printClient($user, $isBuyer, $users, $order) {
		if($user->getId() != 24){
        ?>
        <div class=""><span> Nombre de Usuario: </span>
                <?php if ($users !== null): ?>
                <select name="client_id" class="">
                    <?php
                    foreach ($users as $key => $client) {
                        /* @var $client DefaultDb_Entities_User */
                        if ($client->getId() != Model3_Auth::getCredentials('id') &&
                                $client->getStatus() == DefaultDb_Entities_User::STATUS_ACTIVE &&
                                count($client->getBranches()) > 0) {
                            if (!$isBuyer && $client->getType() == DefaultDb_Entities_User::USER_CLIENT_MAS_DISTRIBUCION) {
                                echo '<option value="' . $client->getId() . '">';
                                echo $client->getCommercialName();
                                echo '</option>';
                            } else if ($client->getType() == DefaultDb_Entities_User::USER_CLIENT) {
                                echo '<option value="' . $client->getId() . '">';
                                echo $client->getCommercialName();
                                echo '</option>';
                            }
                        }
                    }
                    ?>
                </select>
        <?php else:
        		echo $user->getUsername();
        	 ?>
       	</div>
        <?php
		endif;
        }else{
			if($order->getPointBuyer() != NULL){
			?>
			<div class=""><span> Punto de Entrega: </span>
			<?php 
			echo $order->getPointBuyer()->getName();
			?>
			</div>
			<div class=""><span> Direccion destino: </span>
			<?php 
			$calle = explode("-",$order->getPointBuyer()->getAddress()->getAddress());
			echo $calle[0]. "No.".$order->getPointBuyer()->getExtNumber().", ".$order->getPointBuyer()->getNeighborhood();
			?>
			</div><br />
			<?php 
			}else{
				echo "no hay datos";
			}

		}
    }

    private function printProductsDates($order,$isBuyer) {
		if($isBuyer && $order->getShippingStatus() == 0){  

			$dbs = Model3_Registry::getInstance()->get('databases');
			$em = $dbs['DefaultDb'];
			$buyerproducts = $em->getRepository('DefaultDb_Entities_Product')->findBy(array('client' => $order->getSeller()->getId()),array('name' => 'ASC'));
  
    ?>
			<!--  Login form -->
			<div class="modal hide fade in" id="loginForm" tabindex="-1" aria-hidden="false">
			 	<div class="modal-dialog" role="document">
			        <div class="modal-content">
			        	<div class="modal-header">
			              	<div class="modal-headertitle">PRODUCTOS</div>
						</div>
			            <!--Modal Body-->
			            <div class="modal-body">
			            	<div style="display: flex; align-items: center;">
				            	<select id="newproduct" name="newproduct" style="margin-right: 1.5rem;">
				            		<option value="">Selecciona un producto</option>
				            		<?php 
				            		foreach ($buyerproducts as $item){
										if($item->getCatalog()){
											$checkcatalog = ($item->getCatalog()->getId() === 47)?FALSE:TRUE;
										}
										
										if($checkcatalog){
											echo '<option value="'.$item->getId().'">'.$item->getName()." - $".$item->getPrice().'</option>';
										}
										
									}
				            		
				            		?>
				            	</select>
								<button class="allbuttons" style="background-color: green;" onclick="addrow()">Agregar</button>
							</div>	
							<table style="border:1px solid #000; width:100%; margin-top:1em;">
								<thead style="background-color: grey;">
									<tr>
										<th style="min-width:10%">Cantidad</th>
										<th style="min-width:65%">Producto</th>
										<th style="min-width:10%">Precio</th>
										<th></th>
									</tr>
								</thead>
								<tbody id="addproducts">
								</tbody>
								<tfoot>
								<tr><td></td><td></td><td><span id="spantotal">$0.00</span></td></tr>
								</tfoot>
							</table>
							<input type="hidden" id="idorder" value="<?php echo $order->getId(); ?>">
							<button class="allbuttons" style="background-color: orange;" onclick="sendrows()">Incluir en orden</button>
			            </div>
			            <!--/Modal Body-->
			    	</div>
				</div>
			</div>
			<!--  /Login form --> 
      	<?php }?>  
      
        <h4>Informaci&oacute;n costo de Productos</h4>
        <?php echo  ($isBuyer && $order->getShippingStatus() == 0) ? '<a href="#loginForm" id="clickvisual" class="allbuttons" data-toggle="modal" style="background-color:blue">Agregar Productos</a>'  : ''; ?>
        <table class="table tab-content">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
				$ispantry = false;
                foreach ($order->getProducts() as $productInOrder) {
                    $product = $productInOrder->getProduct();
                    $ptotal = $productInOrder->getQuantity() * $product->getPrice();
                    $total += $ptotal; 
                    echo '<tr>';
                    echo '  <td>' . $productInOrder->getQuantity() . '</td>';
                    if ($productInOrder->getVariant())
                        echo '  <td>' . $product->getName() . ' - ' . $productInOrder->getVariant()->getDescription() . '</td>';
                    else
                        echo '  <td>'. $product->getName() . '</td>';
                    echo '  <td>' . $product->getSku() . '</td>';
					
					if($product->getCatalog()){
						if( $product->getCatalog()->getId() == 47){
							echo '<td>promoci&oacute;n</td>';
							$ispantry = true;
						}else{
							//echo  '<td>$' .$product->getPrice(). '</td>';
							echo  '<td>$' . number_format($ptotal,2,'.',''). '</td>';
						}
					}else{
							echo  '<td>$' . number_format($ptotal,2,'.',''). '</td>';
					}
					

					echo '</tr>';
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td>Total</td>
                    <td>$<?php echo ($ispantry)? ' 250.00 ' : number_format($total,2,'.',''); ?></td>
                </tr>
            </tfoot>
        </table>
        <?php
    }

    ///////////////////////////////////////////////
    public function printTipoPago($order) {
        ?>
        <h4>Metodo de Pago</h4>
        <div class="input-group" id="divTipoPago" >
            <form action="<?php echo $this->_view->url(array('action' => 'viewEfectuarPago'), TRUE); ?>" method="post">
                <select class="form-control" id="slcTipoPago" name="slcTipoPago" required="true" >
                    <option value="">Seleccionar...</option>
                    <option value="1">Pago Contra Entrega</option>
                    <option value="2">Pago al Momento</option>
                    <option value="3">Pago Fuera de Mas Distribucion</option>
                </select>
                <input type="submit" name="uno" id="uno" value="" class=" btn-default sin-padding pers-btn icono-pagar tam-normal">
            </form>
        </div>
        <?php
        switch ($order->getShippingStatus()) {
            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_NOT_SHIPPED:
                ?>
                    <div class="alert">
                        <strong>Aun no se genera el Envio </strong> por parte del vendedor.
                    </div>
<?php
                break;
        }
        ?>

        <?php
    }

    public function printComentarios($order) {
        ?>
        <h4>Evidencias y Comentarios</h4>
        <!-- span><textarea style="max-width:400px;max-height: 200px;min-width:200px;min-height: 100px;" disabled="disabled" class="span6"-->
        <?php //echo $order->getComments(); 
        //$string = preg_replace("/<br \/>/","\r", rtrim($this->printComments($order)));
      	//echo preg_replace("/<br \/>/","\r", $this->printComments($order)); 
      	echo $this->printComments($order);
      	?>
        <!-- /textarea></span-->
        <?php
    }

    public function printOrderRepord($order, $base = 12) {
        $comprador = $order->getSeller();
        $vendedor = $order->getBuyer();
        $baseMidle = $base / 2;
        ?>
        <div class="row">
            <div class="span<?php echo $base; ?>">
        <?php $this->printOrderDatesTable($order) ?>
            </div>
        </div>
        <div class="row">
            <div class="span<?php echo $baseMidle; ?>" id="sellerInfo">
                <h3><span class="icon-user"></span> Vendedor</h3>
                <?php $this->_view->Users()->profileUser($comprador); ?>
            </div>

            <div class="span<?php echo $baseMidle; ?>" id="buyerInfo">
                <h3><span class="icon-user"></span> Comprador</h3>
                <?php $this->_view->Users()->profileUser($vendedor); ?>
            </div>
        </div>
        <div class="row">
            <div class="span<?php echo $base; ?> tab-content">
                <?php $this->printShippingDates($order, true); ?>
            </div>
        </div>
        <div class="row">
            <div class="span<?php echo $base; ?>">
        <?php $this->printProductsDates($order,$isBuyer); ?>
            </div>
        </div>
        <?php
    }

    public function printCreateOrder($users, $isBuyer = null) {
        ?>
        <div class="row-fluid">
        	<div class="span12">
        		<?php 
        		//$this->printUserDates(null, $isBuyer, $users); 
        		$this->printUserDates(null, $isBuyer, $users); 
        		?>
            </div>
            <div class="" style="margin-left: 0px !important;"><br>
                <br>
                <h4></span> Datos de la orden</h4>
                <div class="">Fecha compra:
                    <?php echo date('Y-m-d'); ?>
                </div>
                <div class="">Estatus Pago:
                    Sin pagar
                </div>

                <div class="">Estatus recolecci&oacute;n:
                    En espera
                </div>
            </div>
            <div class="" style="max-width: 200px; max-height: 50px;">
                Comentarios:
                <textarea name="comments" class="textarea" class="order" style="max-width: 200px; max-height: 50px;"></textarea>
            </div>
        </div>
        <!--El valor del checkbox se pone en cero para que inicialmente no este visible-->
        <br>
        <br>
        <div class="alert">
            Todas Las ordenes que se agreguen de la fecha a tres dias no se pueden cancelar
        </div>
        <input type="checkbox" style="display:none;" value="0" name="orderR" id="orderR"/> &nbsp; &nbsp; <!--Genera ordenes Recurrentes-->
        <div id="envioR"></div>
        <?php
    }

    public function printSearchClients($isBuyer = NULL,$isbranche = NULL) {
//        foreach ( $this->_view->favoriteUsersClient as $favorite )
//        {var_dump($favorite);die;}
        ?>
        <input type="hidden" name="div_selected" id="div_selected" value="">
        <div id="div_radio_favorites" class="">
                <?php if (is_array($this->_view->favoriteUsersClient) && count($this->_view->favoriteUsersClient) > 0): ?>
                <span>Favoritos:</span>
                <select class="" name="client_favorites_id">
                    <?php
                    foreach ($this->_view->favoriteUsersClient as $favorite) {
                        echo '<option value="' . $favorite["id"]. '">' . $favorite["commercialName"]. '</option>';
                    }
                    ?>
                </select>
        <?php endif; ?>
        </div>

           	<?php if (is_array($this->_view->categories)): ?>
            <div class ="row" id="div_radio_point_fletes">
                <div class="span5>"><span>Categorias:</span>
                    <select id="select_category" class="order">
                        <?php
                        foreach ($this->_view->categories as $category) {
                            echo '<option value="' . $category->getId() . '">' . $category->getName() . '</option>';
                        }
                        ?>
                    </select></div>
                <div class="span5"><span><?php echo $isBuyer ? 'Clientes:' :'Proveedores:' ; ?></span><div id="div_points"></div></div>
            </div>
            <?php endif; ?>
            <?php if (!$this->_view->isBuyer){ ?>
            <div id="div_radio_clients" >
            <!--                <span>Ciudad:</span>
                <select id="select_state" class="order">
                <?php
//                    foreach ($this->_view->states as $state) {
//                        $selected = ($state->getId() == 24) ? 'selected="selected"' : '';
//                        echo '<option value="' . $state->getId() . '" ' . $selected . '>' . $state->getName() . '</option>';
//                    }
                ?>
                </select>-->
                <span>Cliente &oacute; Direcci&oacute;n:</span>
                <input name="usernameTxt" class="order search-query" id="usernameTxt" autocomplete="off" type="text" class="input300" placeholder="Buscar"/><span></span>
                <input name="hiddenBranch" type="hidden" id="hiddenBranch" />
				<?php 
			              echo  ($isbranche)?'<input name="isBranch" type="hidden" id="isBranch" value="1" />':'';
				?>
                <div style="position: relative;">
                    <div class="suggestionUsers-sf" id="suggestedUsers">
                        <div id="suggestedUsersList">nn</div>
                    </div>
                </div>
            </div>
        <?php }else{ ?>
            <div id="div_radio_clients" >
                <span>Proveedor &oacute; Direcci&oacute;n:</span>
                <input name="usernameTxt" class="order search-query" id="usernameTxt" autocomplete="off" type="text" class="input300" placeholder="Buscar"/><span></span>
                <input name="hiddenBranch" type="hidden" id="hiddenBranch" />
                <input type="hidden" id="isbuy" value="1" />
                <div style="position: relative;">
                    <div class="suggestionUsers-sf" id="suggestedUsers">
                        <div id="suggestedUsersList">nn</div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php
    }

    public function getRadioButtonsOptions($option = 1) {
        $radios = '';
        $radios .= '<div class="radios">';
        $checked = '';
        if ($option == 1) {
            if (is_array($this->_view->favoriteUsersClient) && count($this->_view->favoriteUsersClient) > 0) {
                $radios .= '<label class="radio"><input id="radio_favorites" type="radio" class="radio" name="radio" value="1" checked/>Clientes Favoritos</label>';
            } else
                $checked = 'checked';
            if (!$this->_view->isBuyer)
                $radios .= '<label class="radio"><input id="radio_point_fletes" type="radio" class="radio" name="radio" value="2" ' . $checked . '/>Enviar a una direcci&oacute;n</label>';
            if (!$this->_view->isBuyer || count($this->_view->favoriteUsersClient) > 0)
                $radios .= '<label class="radio"><input id="radio_clients" type="radio" class="radio btn btn-default  sin-padding pers-btn icono-enviar-cliente-registrado tam-normal" name="radio" value="3"/>Enviar a un cliente registrado</label>';
        }
        else {
            $radios .= '<div class="btn-group" data-toggle="buttons-radio">';
            if (is_array($this->_view->favoriteUsersClient) && count($this->_view->favoriteUsersClient) > 0 && $this->_view->isBuyer){
                 $radios .= '<button type="button" id="radio_favorites" class="radio btn btn-default  sin-padding pers-btn icono-proveedor-favorito tam-normal active ">Proveedores favoritos</button>';
            }else if (is_array($this->_view->favoriteUsersClient) && count($this->_view->favoriteUsersClient) > 0 && !$this->_view->isBuyer) {
                $radios .= '<button type="button" id="radio_favorites" class="radio btn btn-default  sin-padding pers-btn icono-cliente-favorito tam-normal active ">Cliente favorito</button>';
            } else if (is_array($this->_view->favoriteUsersClient) && count($this->_view->favoriteUsersClient) > 0)
                $checked = 'active';
            /*if (!$this->_view->isBuyer)
                $radios .= '<button type="button" id="radio_point_fletes" class="radio btn btn-default  sin-padding pers-btn icono-enviar-a-direccion tam-normal ' . $checked . '">Enviar a una direcci&oacute;n</button>';*/
            $etiqueta = ($this->_view->isBuyer) ?'Pedir':'Enviar';
            $radios .= '<button type="button" id="radio_point_fletes" class="radio btn btn-default  sin-padding pers-btn icono-enviar-a-direccion tam-normal ' . $checked . '">'.$etiqueta.' a una direcci&oacute;n</button>';
            
            if ($this->_view->isBuyer && count($this->_view->favoriteUsersClient) > 0)
                $radios .= '<button type="button" id="radio_clients" class="radio btn btn btn-default  sin-padding pers-btn icono-proveedor-registrado tam-normal">Enviar a un proveedor registrado </button>';
            else if (!$this->_view->isBuyer || count($this->_view->favoriteUsersClient) > 0)
                $radios .= '<button type="button" id="radio_clients" class="radio btn btn btn-default  sin-padding pers-btn icono-enviar-cliente-registrado tam-normal">Enviar a un cliente registrado</button>';

            $radios .= '</div>';
        }
        $radios .= '</div>';
        return $radios;
    }
    
    /*******************************MOSTRAR COMENTARIOS SOBRE LOS PAQUETES EAAL(14-03-17)*************************************/
    public function printComments($order) {

		$dbs = Model3_Registry::getInstance()->get('databases');
		$em = $dbs['DefaultDb'];
		$transactiondata = $em->getRepository('DefaultDb_Entities_Transactions');
		$routepointactdata = $em->getRepository('DefaultDb_Entities_RoutePointActivity');
		$activitidetdata = $em->getRepository('DefaultDb_Entities_ActivityDetail');
		$Order = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->findOneBy(array('id' =>$order));
		
		$transaction = $transactiondata->findOneBy(array('transactionId' => $order));
		
		$res = ($Order->getComments() != '')?"- ".$Order->getComments(). "<br />":'';//variable para los comentarios
		
		if($transaction){
			$routepointacts = $routepointactdata->findBy(array('transaction' => $transaction->getId()));
			if($routepointacts){
				foreach ($routepointacts as $routeact){
					if($routeact->getStatusReason() != NULL){//si existen comentarios en el campo status_reason de la tabla routepoint_activity se agregan a la variable
						$res .= "- ".$routeact->getStatusReason(). "<br />";
					}
					$activs = $activitidetdata->findOneBy(array('routePointActivityId' => $routeact->getId(), 'causeId' => 1));
					//$activs = $activitidetdata->findOneBy(array('routePointActivityId' => $routeact->getId()));
					if($activs){//si existen comentarios en el campo comentarios y estan en estatus 1 en el campo causeId_id de la tabla activity_detail se agregan a la variable
						$res .= "- ".$activs->getComentarios(). "<br />";
					}
					
					$activs = $activitidetdata->findOneBy(array('routePointActivityId' => $routeact->getId()));
					if($activs){
						if($activs->getUploadFile())
							$this->imgUrl[] = array('imgUrl' =>$this->_view->getBaseUrl().'/images/evidencias/'.$activs->getId().$activs->getUploadFile());
							//$res .= '<img src="'.$this->_view->getBaseUrl().'/images/evidencias/'.$activs->getId().$activs->getUploadFile().'" height="90" width="90"><br /><br />';
					}
				}
			}
		}
		return $res;
    }
    
    /********************************MOSTRAR LOS PUNTOS DONDE YA PASO EL PAQUETE*************************************/
    public function printPointsAc($order) {

		$dbs = Model3_Registry::getInstance()->get('databases');
		$em = $dbs['DefaultDb'];
		$transactiondata = $em->getRepository('DefaultDb_Entities_Transactions');
		$routepointactdata = $em->getRepository('DefaultDb_Entities_RoutePointActivity');
		$activitidetdata = $em->getRepository('DefaultDb_Entities_ActivityDetail');
		$Order = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->findOneBy(array('id' =>$order));
		
		
		$transaction = $transactiondata->findOneBy(array('transactionId' => $order));
		$routepointacts = $routepointactdata->findBy(array('transaction' => $transaction->getId()));
		?>
		<h4>Actividades en los puntos recorridos</h4>
		<?php 
		foreach ($routepointacts as $routeact){
			$activs = $activitidetdata->findOneBy(array('routePointActivityId' => $routeact->getId()));
			if($activs){ ?>
			    <div class="row">
			        <div class=""><?php echo $routeact->getRoutePoint()->getPoint()->getName() ?></div>
			  	</div>
			<?php 
			}
		}
 	}   
 	/********************************MOSTRAR LOS PUNTOS DONDE YA PASO EL PAQUETE*************************************/
 	
 	/********************************OBTENER LA HORA ESTIMADA DE LLEGADA DEL PAQUETE*************************************/
 	public function arriveDate($order,$p) {

		$dbs = Model3_Registry::getInstance()->get('databases');
		$em = $dbs['DefaultDb'];

		$res = "";
		
		try{
		$transactiondata = $em->getRepository('DefaultDb_Entities_Transactions')->findOneBy(array('transactionId' => $order->getId(), 'transactionType' => 1));
		/*$transaction = $em->getRepository('DefaultDb_Entities_Transactions');
		$transactiondata = $transaction->createQueryBuilder('f')
		->where("f.transactionId = :transactionId AND f.transactionType IS NOT NULL")
		->setParameter('transactionId',$order->getId())
		->setMaxResults(1)
		->getQuery()->getSingleResult();*/
		
		//->getSingleResult();
		if($transactiondata){
			//if($transactiondata != NULL){
		
			$RPActv = $em->getRepository('DefaultDb_Entities_RoutePointActivity');
		
			//$criteria = "t.transaction = :tid and t.activityType = :typeId";
			$criteria = "t.transaction = :tid";
			switch ($p){
				case '1':
					$orderby = 'ASC';
					break;
				case '2':
					$orderby = 'DESC';
					break;
						
			}
			$lrpAct = $RPActv->createQueryBuilder('t')
			->where($criteria)
			->setMaxResults(1)
			->orderBy('t.id', $orderby)
			->setParameter('tid',$transactiondata->getId())
			//           ->setParameter('typeId',$p) // GS_VALIDACION
			->getQuery()->getSingleResult();
		
			$tid = $lrpAct->getRoutePoint()->getId();//SE OBTIENE PUNTO DONDE SE ENTREGARA EL PAQUETE
		
			if($order->getPointBuyer() != NULL){//
				//$selPoint =  $order->getPointBuyer()->getId();
					
				$secActves = $em->getRepository('DefaultDb_Entities_SequentialActivities');
					
				$criteria1 = "t.order = :orid";
				$criteria1 .= ' AND t.routePoint = :rpid';
		
				$SAct = $secActves->createQueryBuilder('t')
				->where($criteria1)
				->setMaxResults(1)
				->orderBy('t.id', 'ASC')
				->setParameter('orid', $order->getId())
				->setParameter('rpid',$tid)
				->getQuery()->getOneOrNullResult();
				if($SAct!=NULL){  // GS_VALIDACION
					if($SAct->getShippingDateAct() != NULL){
						$res = substr($SAct->getShippingDateAct()->format('Y-m-d H:i:s'), 0,16);
					}else{
						$res = substr($SAct->getShippingDate()->format('Y-m-d H:i:s'), 0,16);
					}
				}
			}
		}
		
		}catch (NoResultException $e){
		}
		return $res;
	}   
	/********************************OBTENER LA HORA ESTIMADA DE LLEGADA DEL PAQUETE*************************************/
 	
}
