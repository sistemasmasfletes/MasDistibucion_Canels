<?php /* @var $view Model3_View */?>
<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Panel</li>
    <li class="active actualpg ">Pedidos</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Estatus de mis pedidos</h1>
                <?php
                    $totalBranches = (int)$view->branchStatus['totalBranches'];
                    $branchesPending = (int)$view->branchStatus['branchesPending'];
                    $branchesValidated = (int)$view->branchStatus['branchesValidated'];
                    $contractsValidated = (int)$view->branchStatus['contractsValidated'];
                    
               	if($contractsValidated ===1 ){
	                    if($totalBranches>0 && $branchesValidated>0){
							echo '<a class="allbuttons" style="float:right;background-color:#d74710;" href="' . $view->url(array('controller'=>'BackStore','action'=>'createOrder','module'=>'User', 'buyer' => '1')) . '" ><img src="'.$view->getBaseUrl('/images/iconos/pluswhite.png').'" style="margin-right:1em;" />Generar pedido</a>';
	                ?>
		            	<!-- a class="btn-default sin-padding" href="" title="Agrega una orden" >
		            		<span class="pers-btn icono-agregagar-orden tam-normal" style="float: right;  box-shadow:-19px 12px 17px -7px rgba(13,12,12,0.58); border: none;"></span>
		            	</a-->
	                	<div class="clear">&nbsp;</div>

	                <?php 
	                if(count($view->orders)>0){
	                ?>
	                
		                <div style="display: flex; width:100%;">
				            <div style="width:50%;margin-top:1em;">
				               	<input class="btnright" type="text" placeholder="Buscar" id="kwd_search" value=""/>
				            </div>
				        </div>
	                
		                <div class="contable">
				    	    <table class="table table-striped table-bordered tableresp">
	                    	    <thead>
	                        	    <tr>
	                                <th>Folio</th>
	                                <!-- th>Recurrente</th-->
	                                <th>Vendedor</th>
	                                <!-- th>Pagado</th-->
	                                <th>Estado del paquete</th>
	                                <th>M&eacute;todo de pago</th>
	                                <th>Fecha de pedido</th>
	                                <th>Fecha de recolección estimada</th>
	                                <th>Fecha y hora de entrega estimada</th>
	                                <th>Detalles</th>
	                                <th>C&oacute;digo QR</th>
	                                <th>Opciones</th>
	                        	</tr>
	                        </thead>
	                        <tbody id="myTable">
	                            <?php
	                            $em = $view->_em;
	                                foreach ($view->orders as $order)
	                                {
	                                     $pago = $em->getRepository('DefaultDb_Entities_Pagos')
	                                            ->createQueryBuilder('m')
	                                            ->where("m.compraVenta = :order")
	                                            ->setParameters(array("order" => $order))
	                                            ->setMaxResults(1)
	                                            ->getQuery()->getOneOrNullResult();
	                                    //si son ordenes simples
	                                   $img = '<img src="'.$view->getBaseUrl().'/images/ok-icon.png"/>';
	                                    if($order->getRecurrent() == DefaultDb_Entities_M3CommerceOrder::RECURRENT_NOTACTIVE)
	                                    { 
	                                ?>
	                                    <tr>
	                                        <td><?php echo $order->getId(); ?></td>
	                                        <!-- td><?php //echo $img = ($order->getOrderParent() != null)? $img : "";?></td-->
	                                        <td><?php echo $order->getSeller()->getCommercialName(); /*echo $order->getContactR(); echo $order->getContactS(); echo $order->getContent();*/ ?></td>
	                                        <!-- td><?php //echo $order->getPaymentStatusString(); ?></td-->
	                                        <td><?php echo $order->getShippingStatusString(); ?></td>
	                                        <td><?php echo ($pago != NULL) ?  $pago ->getTipoDebito()->getTipoDebito() : "";?></td>
	                                        <td><?php echo $order->getCreationDate() != null ? $order->getCreationDate()->format('Y-m-d') : ""?></td>
											<td><?php 
													//echo $order->getShippingDate() != null ? $order->getShippingDate()->format('Y-m-d') : "No programado";
													if($view->Orders()->arriveDate($order,'1') != ""){
														echo substr($view->Orders()->arriveDate($order,'1'),0,10);
		                                        		$dshp =  new DateTime($view->Orders()->arriveDate($order,'1'));
														
														//echo '<a class=" btn-default sin-padding" href="'.$view->url(array('controller' => 'BackStore', 'action' => 'getVolR', 'param' => $order->getId()), true).'" ><span class="pers-btn icono-refrescar-fila icono-tam-tabla"></span></a>';
														echo '<br/><a class=" btn-default sin-padding" href="'.$view->url(array('controller' => 'BackStore', 'action' => 'RefreshDel', 'param' => $order->getId()), true).'" ><span class="pers-btn icono-refrescar-fila icono-tam-tabla"></span></a>';
													}else{
														echo "No programado";
													}
													
											?></td>
		                                    <td><?php 
													if($view->Orders()->arriveDate($order,'2') != ""){
		                                        		echo substr($view->Orders()->arriveDate($order,'2'),0,10);
		                                        		//echo '<a class=" btn-default sin-padding" href="'.$view->url(array('controller' => 'BackStore', 'action' => 'getVolR', 'param' => $order->getId()), true).'" ><span class="pers-btn icono-refrescar-fila icono-tam-tabla"></span></a>';
													 	echo '<br/><a class=" btn-default sin-padding" href="'.$view->url(array('controller' => 'BackStore', 'action' => 'RefreshArr', 'param' => $order->getId()), true).'" ><span class="pers-btn icono-refrescar-fila icono-tam-tabla"></span></a>';
		                                        	}else{
														echo "No programado";
													}
													
											?></td>
											<td><?php
	                                       		echo '<a class="btn btn-default btn18px" href="'.$view->url(array('controller' => 'BackStore', 'action' => 'viewOrderShopping', 'id' => $order->getId()),true).'"><span class="pers-btn icono-ver-actividad icono-tam-tabla"></span></a>';
	
	                                       		if($order->getProgramer() != NULL){
	                                       			if($order->getProgramer() == $order->getSeller()->getId()){
	                                       				echo 'programado por vendedor';
	                                       		
	                                       			}else{
	                                       				echo 'programado por comprador';
	                                       		
	                                       			}
	                                       		}
	                                       		
	                                       		?></td>
	                                        <td><a class="btn btn-default btn18px" name="idPoint" onclick="qrcode('<?php echo $order->getId();?>')"  value="<?php echo $order->getId();?>"  title="Generar c&oacute;digo QR">
	                                        <span class="pers-btn icono-qr icono-tam-tabla"></span>
	                                        </a></td>
		                                    <td style="width:25%;">
		                                    <?php 
		                                        	if($order->getShippingStatus() == 0){
														echo '<a class="allbuttons buttonin" style="float:left;background-color:#212121;" href="' . $view->url(array('action' => 'creteShipping', 'id' => $order->getId()), true) . '" ><img src="'.$view->getBaseUrl('/images/iconos/trucktimerwhite.png').'" style="margin-right:0.5em;" />Programar envio</a>';
														echo '<a class="allbuttons buttonin" style="float:left;background-color:#ff6e36;" href="' . $view->url(array('controller' => 'BackStore', 'action' => 'CancelOrdS', 'param' => $order->getId(), 'frm' => 'shopping'), true) . '" ><img src="'.$view->getBaseUrl('/images/iconos/tachawhite.png').'" style="margin-right:0.5em;" />Detener paquete</a>';
														/*echo '<a class=" btn-default sin-padding" href="'.$view->url(array('action' => 'creteShipping', 'id' => $order->getId()), true).'" ><span class="pers-btn icono-generar-pedido tam-normal"></span></a>';
														echo '<a class=" btn-default sin-padding" href="'.$view->url(array('controller' => 'BackStore', 'action' => 'CancelOrdS', 'param' => $order->getId()), true).'" ><span class="pers-btn icono-detener-paquete tam-normal"></span></a>';*/
															
													}else{
														$dnow =  new DateTime("now");
														$dif = $dnow->diff($dshp);
														if(strpos($dif->format('%R%a'), '-') === FALSE && (int)$dif->format('%d') > 1 && $order->getShippingStatus() == 1){
															echo '<a class="allbuttons buttonin" style="float:left;background-color:#ff6e36;" href="' . $view->url(array('controller' => 'BackStore', 'action' => 'CancelOrdC', 'param' => $order->getId(), 'frm' => 'shopping'), true) . '" ><img src="'.$view->getBaseUrl('/images/iconos/tachawhite.png').'" style="margin-right:0.5em;" />Detener paquete</a>';
															//echo '<a class=" btn-default sin-padding" href="'.$view->url(array('controller' => 'BackStore', 'action' => 'CancelOrdC', 'param' => $order->getId()), true).'" ><span class="pers-btn icono-detener-paquete tam-normal"></span></a>';
														}
														echo '<a class="allbuttons buttonin" style="float:left;background-color:#b83909;" href="' . $view->url(array('module' => 'User', 'controller' => 'Dompdf', 'action' => 'index', 'id' => $order->getId())) . '" ><img src="'.$view->getBaseUrl('/images/iconos/documentpdfwhite.png').'" style="margin-right:0.5em;" />Generar etiqueta</a>';
														//echo '<a class="btn-default sin-padding" target="_blank" href="'. $view->url(array('module' => 'User', 'controller' => 'Dompdf', 'action' => 'index', 'id' => $order->getId())).'" ><span class="pers-btn icono-generar-etiqueta tam-normal"></span></a>';
													}
		                                        ?></td>                                        
	                                    </tr>
	                                <?php
	                                    }
	                                }
	                            ?>
	
	                        </tbody>
	                    </table>
	                   	<ul class="pagination pagination-lg pager" id="myPager"></ul>
	                    
	                    <?php if($view->recurrentsFlag):?>
	                      <table class="table table-striped table-bordered">
	                        <thead>
	                            <tr>
	                                <th>Folio</th>
	                                <th>Recurrente</th>
	                                <th>Vendedor</th>
	                                <th>Pagado</th>
	                                <th>Entregado</th>
	                                <th>Fecha de pedido</th>
	                                <th>Estatus</th>
	                                <th>Detalles</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                            <?php
	                                foreach ($view->orders as $order)
	                                {//si son ordenes recurrentes
	                                    if($order->getRecurrent() == DefaultDb_Entities_M3CommerceOrder::RECURRENT_ACTIVE)
	                                    { 
	                                ?>
	                                    <tr>
	                                        <td><?php echo $order->getId(); ?></td>
	                                        <td><?php echo $img = ($order->getOrderParent() != null)? $img : "";?></td>
	                                        <td><?php echo $order->getSeller()->getFullName(); ?></td>
	                                        <td><?php echo $order->getPaymentStatusString(); ?></td>
	                                        <td><?php echo $order->getShippingStatusString(); ?></td>
	                                        <td><?php echo $order->getCreationDate() != null ? $order->getCreationDate()->format('Y-m-d') : ""?></td>
	                                        <td><?php echo '<a href="'.$view->url(array('controller' => 'BackStore', 'action' => 'viewOrderShopping', 'id' => $order->getId())).'">Orden '.$order->getId().'</a>';?></td>
	                                    </tr>
	                                <?php
	                                    }
	                                }
	                            ?>
	
	                        </tbody>
	                    </table>
	                    <?php endif; ?>
	                </div>
	                <?php
	                }else{
	                    echo '<div class="alert alert-error">Actualmente no tiene compras.</div>';
	                }
	                
	                }else{
	                	?>
 	                    <div class="alert">
   	                        No puede generar ordenes debido a que no tiene sucursales dadas de alta o sus sucursales aun no han sido aprobadas.
   	                    </div>
       	                <?php
  	                }
	                	                
                }else{
                ?>
               		<div class="alert">
                    	Nuestras pol&iacute;ticas y t&eacute;rminos de uso han cambiado por favor reviselos y autoricelos en la secci&oacute;n de Contratos.
                  	</div>
             	<?php 
              	}?> 
            </div>
        <!--</div>-->
    </div>
</div>


<script type="text/javascript">

	$.fn.pageMe = function(opts){
	    var $this = this,
	        defaults = {
	            perPage: 8,
	            showPrevNext: false,
	            hidePageNumbers: false
	        },
	        settings = $.extend(defaults, opts);
	    
	    var listElement = $this;
	    var perPage = settings.perPage; 
	    var children = listElement.children();
	    var pager = $('.pager');
	    
	    if (typeof settings.childSelector!="undefined") {
	        children = listElement.find(settings.childSelector);
	    }
	    
	    if (typeof settings.pagerSelector!="undefined") {
	        pager = $(settings.pagerSelector);
	    }
	    
	    var numItems = children.size();
	    var numPages = Math.ceil(numItems/perPage);
	
	    pager.data("curr",0);
	    
	    if (settings.showPrevNext){
	        $('<li><a href="#" class="prev_link"> < </a></li>').appendTo(pager);
	    }
	    
	    var curr = 0;
	    while(numPages > curr && (settings.hidePageNumbers==false)){
	        $('<li><a href="#" class="page_link">'+(curr+1)+'</a></li>').appendTo(pager);
	        curr++;
	    }
	    
	    if (settings.showPrevNext){
	        $('<li><a href="#" class="next_link"> > </a></li>').appendTo(pager);
	    }
	    
	    pager.find('.page_link:first').addClass('active');
	    pager.find('.prev_link').hide();
	    if (numPages<=1) {
	        pager.find('.next_link').hide();
	    }
	  	pager.children().eq(1).addClass("active");
	    
	    children.hide();
	    children.slice(0, perPage).show();
	    
	    pager.find('li .page_link').click(function(){
	        var clickedPage = $(this).html().valueOf()-1;
	        goTo(clickedPage,perPage);
	        return false;
	    });
	    pager.find('li .prev_link').click(function(){
	        previous();
	        return false;
	    });
	    pager.find('li .next_link').click(function(){
	        next();
	        return false;
	    });
	    
	    function previous(){
	        var goToPage = parseInt(pager.data("curr")) - 1;
	        goTo(goToPage);
	    }
	     
	    function next(){
	        goToPage = parseInt(pager.data("curr")) + 1;
	        goTo(goToPage);
	    }
	    
	    function goTo(page){
	        var startAt = page * perPage,
	            endOn = startAt + perPage;
	        
	        children.css('display','none').slice(startAt, endOn).show();
	        
	        if (page>=1) {
	            pager.find('.prev_link').show();
	        }
	        else {
	            pager.find('.prev_link').hide();
	        }
	        
	        if (page<(numPages-1)) {
	            pager.find('.next_link').show();
	        }
	        else {
	            pager.find('.next_link').hide();
	        }
	        
	        pager.data("curr",page);
	      	pager.children().removeClass("active");
	        pager.children().eq(page+1).addClass("active");
	    }
	    $(".pager a").css('padding',"3px 4px")
	};

    $(document).ready(function(){
        
  	  $('#myTable').pageMe({pagerSelector:'#myPager',showPrevNext:true,hidePageNumbers:false,perPage:8});
  	  
		$("#kwd_search").keyup(function(){
			if( $(this).val() != ""){
				$("#myTable>tr").hide();
				$("#myTable td:contains-ci('" + $(this).val() + "')").parent("tr").show();
			}else{
				$("#myTable>tr").show();
			}
		});

	});
	// jQuery expression for case-insensitive filter
	$.extend($.expr[":"], 
	{
	    "contains-ci": function(elem, i, match, array) 
		{
			return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
		}
	});    
</script>

