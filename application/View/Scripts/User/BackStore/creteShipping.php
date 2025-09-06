<?php /* @var $view Model3_View */
$packs = $view->packages;
?>
<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Panel</li>
    <li class="active "> <?php
        $credentials = Model3_Auth::getCredentials();
    	//$action = ($view->order->getBuyer()->getId() == $credentials['id']) ? 'shopping' : 'orders';
    	
    	if(($view->order->getBuyer()->getId() == $credentials['id'])){
		
			$action = 'shopping';
			$action1 = 'viewOrderShopping';
			$label1 = 'Recolecciones';

		}else{

			$action = 'orders';
			$action1 = 'viewOrder';
			$label1 = 'Entregas';
				
		}
    	
        echo '<a href="' . $view->url(array('action' => $action)) . '" >'.$label1.'</a>';
        ?></li>
    <li class="active "> <?php
        //$action = ($view->order->getBuyer()->getId() == $credentials['id']) ? 'viewOrderShopping' : 'viewOrder';
        echo '<a href="' . $view->url(array('action' => $action1),true) . '" >Ordenes</a>';
        ?></li>
    <li class="active actualpg ">Generar Pedido</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <?php
                $credentials = Model3_Auth::getCredentials();
                $action = ($view->order->getBuyer()->getId() == $credentials['id']) ? 'viewOrderShopping' : 'viewOrder';
                ?>
                <!--<a class="btn" href="<?php // echo $view->url(array('action' => $action), true); ?>" >Regresar</a>-->
                <div class="alert alert-info">
                    Todas las recolecciones agregadas de hoy a tres dias no se pueden cancelar
                </div>
                <?php 
                if($view->payments != NULL){
					echo '<div class="alert alert-info">'.$view->payments->getTipoDebito()->getTipoDebito().'</div>';
				}
                ?>
                <h3>Cotizaci&oacute;n de Embarque</h3><br />
                <form method="post" action="" id="formPackages">
               		<div id="packaging">        
               			<div id="seldiv" class="caution alert-info" style="display: none; margin-bottom:1.5em; padding:1em;">
               				<span style=" color:#000;">Este paquete contiene varios productos,<br /> &iquest;Que deseas hacer?</span><br />
                            <select id="selgroup" style="margin-top:1em;">
                            	<option value="">Seleccione una Opci&oacute;n</option>
                            	<option value=1>Agrupar en un paquete</option>
                            	<option value=2>Dividir en varios paquetes</option>
                            </select>
                        </div>
               			<div id="selected" class="caution alert-info" style="display: none; margin-bottom:1.5em; padding:1em;">
               				<span id="sel1" style=" color:#000;display:none">TODOS LOS PRODUCTOS ESTAR&Aacute;N CONTENIDOS EN UN PAQUETE</span>
               				<span id="sel2" style=" color:#000;display:none">CADA PRODUCTOS ESTAR&Aacute; CONTENIDO EN UN PAQUETE INDIVIDUAL</span>
							<br /><a href="#" onclick="location.reload()" class="btn" style="width:200px; height:20px" title="" id="">Volver a Elegir</a>
                        </div>
                        
                        <div class="col-sm" style="display:none;">
                            <span>Buscar paquete</span>
                            <select id="suggestedUsersListS"></select>
                            <!-- input name="packageText" class="order col-md" id="packageText" autocomplete="off" type="text" class="input300" placeholder="Buscar"/><span></span>                        
                            <div style="position: relative;">
                                <div class="suggestionUsers-sf" id="suggestedUsers" style="left:0px">
                                    <div id="suggestedUsersList"></div>
                                </div>
                            </div-->
                        </div><br />
                		<?php $view->helper('ECommerce')->selectPointsOrigenDestiny(); ?>     
                        
                        <div id="tablePackage" style="display:none; margin-top:1.5em;">
                            <table class="table">
                                <thead style="border:solid 1px #AEAEAE; background-color:#E5E5E5;">
                                    <tr>
                                    <th>Embalaje</th>
                                    <th style="text-align:center; width:12%">Unidades</th>
                                    <th style="text-align:center; width:12%">Medidas(cm)</th>
                                    <th style="text-align:center; width:12%">Peso(Kg)</th>
                                    <!-- th>Precio uni. cred.</th-->
                                    <th style="text-align:center; width:12%">Total cred.</th>
                                    <th style="text-align:center; width:12%">Total $</th>
                                    <th style="text-align:center; width:12%">Promoción</th>
                                    <th style="text-align:center; width:12%">Eliminar</th></tr>
                                </thead>
                                <tbody id="tablePackageBody" style="border:solid 1px #AEAEAE;">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td colspan="3"style="text-align:right"><h4><span>COSTO TOTAL DEL EMBARQUE</span></h4></td>
                                        <td style="text-align:center"><h4><span id="sumTotal">0.00</span></h4></td>
                                        <td style="text-align:center"><h4><span id="sumcTotal">$ 0.00</span></h4></td>
                                        <td colspan="3"></td>
                                    </tr>
                            	</tfoot>
                            </table>
                            <br />
                            <table style="width: 100%; border-bottom:solid 1px #AEAEAE">
                            	<thead style="text-align:center;border:solid 1px #AEAEAE;background-color:#E5E5E5;">
                            		<tr>
                            		<td colspan="4"><b>INFORMACI&Oacute;N ADICIONAL REQUERIDA</b></td>
                            		</tr>
                            	</thead>
                               <tr style="padding-top: 1.5em;">
                              		<td style="text-align:center;"><b>Descripci&oacute;n de contenido</b></td>
                                    <td><input id="contentdesc" name="contentdesc" type="text" style="width:80%" /></td>
                                    <td style="text-align:center;"><b>Contacto para recolecci&oacute;n</b></td>
                                    <td><input id="contactsend" name="contactsend" type="text" style="width:80%" /></td>
                           		</tr>
                                <tr>
                                	<td style="text-align:center;"><b>Contacto para entrega</b></td>
                                    <td ><input id="contactres" name="contactres" type="text" style="width:80%" /></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>   
                              
                    <div class="alert" style="margin-top: 1em;">
                        <div id="infoDate"></div>
                        <div id="shippingTable">
                            <table>
                                <thead>
                                    <!-- tr><th>Fecha</th><th style="text-align:left">Actividad</th><th style="padding-left:10px;padding-right:10px;">Tarifa</th><th>Costo</th></tr-->
                                    <tbody id="shippingActivities"></tbody>
                                </thead>
                            </table>
                        </div>
                    </div>
                	<table class="table">
                    	<thead>
                        </thead>
                        <tbody id="tablePackageBody">
                        </tbody>
                        <tfoot>
                          	<tr>
                                <td colspan="7" style="text-align:right"><button id="submitButton" class="btn btn-default sin-padding" type="submit"><span class="pers-btn icono-generar-pedido tam-normal"></span></button></td>                                    
                        	</tr>
                        	<tr>
                        		<td><h3><span class="icon-list-alt"></span> Informaci&oacute;n de Saldo</h3></td>
                        	</tr>
                        	<tr>
                            	<td colspan="6">
                                    <table style="width: 100%">
                                    	<tbody>
                                        	<tr>
                                            	<td style="text-align: right"><b>Saldo Acutal:</b></td>
                                                <td style="text-align: left">$ <?php echo $view->ClienteConversion ?></td>
                                                <td style="text-align: right"><b>Conversi&oacute;n a Cr&eacute;ditos:</b></td>
                                                <td style="text-align: left"><span style="color: green"><?php echo $view->ClientCreditos ?></span><input type="hidden" id="ClientCreditos" value="<?php echo $view->ClientCreditos ?>"/></td>
                                            	<td><input type="hidden" id="urlBase" value="<?php echo $view->urlBase ?>"/></td>
                                       		</tr>
                                   		</tbody>
                              		</table>
                           		</td>
                            	<td colspan="2"></td>  
                            </tr>
                    	</tfoot>
              		</table>
                    <input type="hidden" name="shippingDate" id="shippingDate" value="">
              		<input type="hidden" name="tweight" id="tweight" value="">
              		<input type="hidden" name="tvol" id="tvol" value="">
              		<!--input type="hidden" name="shippingDate" id="shippingDate" value=""-->
              		<div class="clear" style="margin-top: 15px;"></div>
                    <input type="hidden" id="idClient" value="<?php echo $view->idClient ?>"/>
                </form>
                <div id="divAdd"></div>
                <div id="searchPromotionDialog" style="display:none">
                    <span>Buscar promoción</span>
                    <select id="suggestedPromoList"></select>                    
                </div>
            </div>
        <!--</div>-->
    </div>
</div>
