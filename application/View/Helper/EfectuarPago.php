<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * @author H4845
 */
class View_Helper_EfectuarPago extends Model3_View_Helper
{   
    public function printOrder($order,$isBuyer,$pago)
    {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        $packagesAdapter = $em->getRepository('DefaultDb_Entities_PackageToOrder');
        $packs = $packagesAdapter->findBy(array('order'=>$order));

        $vendedor = $order->getSeller();
        $comprador = $order->getBuyer();
        ?>
        <style>
            input ,select{ display: block; }
        </style>
            
            <h1>Efectuar Pago</h1>
            <?php
                $action = $isBuyer?'viewOrder':'viewOrderShopping';
//                echo '<a id="aRegresar" class="btn" href="' . $this->_view->url(array('action' => $action),true) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
            ?>
            <input type="hidden" id="txtBaseUrl" name="txtBaseUrl" value="<?php echo $this->_view->getBaseUrl() ?>" disabled="true">
            <input type="hidden" id="txtIdOrden" name="txtIdOrden" value="<?php echo $order->getId() ?>" disabled="true">
            <input type="hidden" id="txtTipo" name="txtTipo" value="<?php echo $isBuyer ?>" disabled="true">
            <div class="row">
                
                
                <?php if(count($order->getProducts())): ?>
                <div class="span6">
                    <?php $this->printProductsDates($order, 2);?>
                </div>
                <div class="span6">
                    <?php $this->printDebitar($pago);?>
                </div>
                
                <?php endif; ?>
                
                 <?php if(!count($order->getProducts()) /*|| ( !count($order->getPackages()) && $order->getShippingStatus() == DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_NOT_SHIPPED)*/): ?>
                <!--<script type="text/javascript">-->
                    <!--alert("¡La orden no tiene productos asociados!");-->
                    <!--window.location = "<?php // echo $this->_view->url(array('controller' => 'BackStore', 'action' => 'viewOrderShopping', 'id' => $order->getId())) ?>";-->
                <!--</script>-->
                
                <?php endif; ?>
                
                
                <?php if($order->getRecurrent() == DefaultDb_Entities_M3CommerceOrder::RECURRENT_ACTIVE): ?>
                <div class="span6">
                    <?php $this->printOrderRecurrent($order);?>
                </div>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="span5">
                <?php if(Model3_Auth::getCredentials('type') == DefaultDb_Entities_User::USER_DRIVER)
                    {
                        if($order->getShippingStatus() == DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED
                           || $order->getShippingStatus() == DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED)
                        {
                            $strBtn = 'Recolectado';
                            if($order->getShippingStatus() == DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED)
                            {
                                $strBtn = 'Entregado';
                            }
                            
                            echo '<input class="btn" type="button" id="changeStatusShippingBtn" value="'.$strBtn.'"/>';
                            echo '<div id="divResultShipping"></div>';
                        }
                    }
                ?>
                </div>
            
            </div>
            <?php
    }

    private function printOrderRecurrent($order)
    {
        ?>
        <h4>Estado de la Orden</h4>
        <select id="changeStatusOrder">
            <?php $selected = ($order->getOrderStatus() == DefaultDb_Entities_M3CommerceOrder::ORDER_STATUS_ACTIVE)? "selected = 'selected'" : ''; ?>
            <option value="<?php echo DefaultDb_Entities_M3CommerceOrder::ORDER_STATUS_ACTIVE ?>" <?php echo $selected; ?>>Activa</option>
            <?php $selected = ($order->getOrderStatus() == DefaultDb_Entities_M3CommerceOrder::ORDER_STATUS_INTACTIVE)? "selected = 'selected'" : ''; ?>                    
            <option value="<?php echo DefaultDb_Entities_M3CommerceOrder::ORDER_STATUS_INTACTIVE?>"<?php echo $selected; ?>>Inactiva</option>
        </select>
        <?php
    }


    private function printOrderDates($order,$isBuyer)
    {
        ?>
        <h4>Datos de la orden</h4>
        <?php if($order->getOrderParent()): ?>
            <h5>Esta orden fue generada de manera recurrente</h5>
        <?php endif; ?>
        <span> Folio: </span>
        <input type="text" readonly="readonly" value="<?php echo $order->getId();?>">

        <span> Fecha compra: </span>
        <input type="text" readonly="readonly" value="<?php ?>">

        <span> Estatus: </span>
        <?php
            if($isBuyer)
            {
            ?>
            <select id="statusPaid">
                <option value="<?php echo DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_NOT_PAID;?>"
                        <?php echo $order->getPaymentStatus() == DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_NOT_PAID?'selected="selected"':'';?>>
                    <?php echo $order->getPaymentStatusString(DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_NOT_PAID);?>
                </option>
                <option value="<?php echo DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_PAID?>"
                        <?php echo $order->getPaymentStatus() == DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_PAID?'selected="selected"':'';?>>
                    <?php echo $order->getPaymentStatusString(DefaultDb_Entities_M3CommerceOrder::PAYMENT_STATUS_PAID);?>
                </option>
            </select>
            <?php
            }
            else
            {
            ?>
            <input type="text" readonly="readonly" value="<?php echo $order->getPaymentStatusString();?>">
            <?php
            }
        ?>
        

        <span> Estatus: </span>
        <input type="text" readonly="readonly" id="statusShipping" value="<?php echo $order->getShippingStatusString();?>">
        <?php
    }

    public function fncTipoPago($pago)
    {

        switch ($pago)
        {
            case 1:
                return "Pago Contra Entrega";
            case 2:
                return "Pago al Momento";
            case 3:
                return "Pago Fuera de Mas Distribución";
        }
        
    }

    private function printDebitar($pago)
    {
        $tipoPago = $this->fncTipoPago($pago);
        ?>
        <h3><span class="icon-list-alt"></span> Datos de la orden</h3>
        <table >
            <tbody>
            <tr>
                <td>Tipo de Moneda:</td>
                <td> 
                    <input type="text" name="moneda" readonly="true" id="moneda"/>
                    <input type="hidden" name="slcTipoMoneda" id="slcTipoMoneda"/>
                </td>
            </tr>
            <tr>
                <td>Creditos Disponibles:</td>
                <td> 
                    <span>
                        <input type="text" id="txtCreditos" name="txtCreditos" disabled="true"></input>
                    </span>
                </td>
            </tr>
            <tr>
                <td>Saldo:</td>
                <td><input type="text" id="txtSaldo" name="txtSaldo" disabled="true"></input></td>
            </tr>
             <tr>
                <td>Tipo de Pago</td>
                <td><input type="text" id="txtTipoPago" name="txtTipoPago" value="<?php echo $tipoPago ?>" disabled="true"></input></td>
                <input type="hidden" id="txtIdTipoPago" name="txtIdTipoPago" value="<?php echo $pago ?>" disabled="true"></input>
             </tr>
            <tr>
                <td></td>
            </tr>
            </tbody>
            <tr>
                <td></td>
            
                <td><input class="btn btn-default sin-padding pers-btn icono-pagar tam-normal" type="button" id="btnGuardar" onclick="fncVerificar()" value=""></td>
            </tr>
        </table>
        
        <?php
    }
    private function printMonedas()
    {
        ?>
        <select>
            <option value="0">USD</option>
            <option value="1">MXN</option>
            <option value="2">EURO</option>
            <option value="3">BOLIVAR</option>
            <option value="3">JIN</option>
        </select> 
        
        <?php
    }

    private function printShippingDates($order,$isBuyer)
    {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        $packagesAdapter = $em->getRepository('DefaultDb_Entities_PackageToOrder');
        $packs = $packagesAdapter->findBy(array('order'=>$order));

//        if($order->getOrderParent())
//            $order = $order->getOrderParent();
        echo '<h4>Embalaje de productos</h4>';
        switch($order->getShippingStatus())
        {
            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_NOT_SHIPPED:
                $credentials = Model3_Auth::getCredentials();
                if($credentials['type'] != DefaultDb_Entities_User::USER_ADMIN):
                ?>
                    <a class="btn" href="<?php echo $this->_view->url(array('action' => 'creteShipping'),true);?>" >Generar Pedido</a>
                <?php 
                else:
                    ?>
                    <div class="alert">
                    <strong>Aun no se genera el Envio </strong>.
                    </div>
                    <?php
                endif;
                if(!$isBuyer):
                ?>
                <div class="alert">
                    <strong>Aun no se genera el Envio </strong> por parte del vendedor,
                    si gusta lo podra generar y el costo del envio se le hara efectivo a usted.
                </div>
                   <?php 
                endif;
                
                break;
            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED:
            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED:
            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_DELIVERED:

                    if(is_array($packs) && count($packs)>0)
                    {
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
                                foreach($packs as $p)
                                {
                                    ?>
                                <tr>
                                    <td><?php echo $p->getNumPackage();?></td>
                                    <td><?php echo $p->getNamePackage()?></td>
                                    <td><?php echo $p->getPrice();?></td>
                                    <td><?php echo $p->getTotalPrice(); $total+=$p->getTotalPrice();?></td>
                                </tr>
                                    <?php
                                }
                                ?>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"><a target="_blank" href="<?php echo $this->_view->url(array('module'=>'User', 'controller' => 'Dompdf', 'action' => 'index','id' => $order->getId()));?>" class="btn">Generar etiquetas</a></td>
                                    <td><b><?php echo $total;?></b></td>
                                </tr>
                            </tfoot>
                        </table>
                    <?php
                    }
                break;
        }
    }

    private function printUserDates($user=null,$isBuyer=true,$users=null)
    {
        echo $isBuyer ? '<h4>Vendedor</h4>':'<h4>Cliente</h4>';
        if($this->_view->isCreateOrder==true)
        {
            $this->printSearchClients();
        }
        else
        {
            $this->printClient($user,$isBuyer, $users);
        }
        ?>
        <span> Correo: </span>
        <input class="order" id="email" type="text" <?php echo ($user?'readonly="readonly"':'disabled="disabled"');?> value="<?php echo ($user !== null ? $user->getUsername():'');?>">
        <span> Telefono: </span>
        <input class="order" id="phone" type="text" <?php echo ($user?'readonly="readonly"':'disabled="disabled"');?> value="<?php echo ($user !== null ? $user->getLocalNumber():'');?>">
        <span> Celular: </span>
        <input class="order" id="movil" type="text" <?php echo ($user?'readonly="readonly"':'disabled="disabled"');?> value="<?php echo ($user !== null ? $user->getCellPhone():'');?>">
        <?php
    }
    
    private function printClient($user,$isBuyer,$users)
    {
        
        ?>
        <span> Nombre: </span>
        <?php if($users!==null): ?>
        <select name="client_id" class="order">
            <?php 
            foreach ($users as $key => $client) 
            {
                /* @var $client DefaultDb_Entities_User */
                if($client->getId()!=Model3_Auth::getCredentials('id')&&
                        $client->getStatus() == DefaultDb_Entities_User::STATUS_ACTIVE && 
                        count($client->getBranches()) > 0)
                {
                    if( !$isBuyer && $client->getType() == DefaultDb_Entities_User::USER_CLIENT_MAS_DISTRIBUCION )
                    {
                        echo '<option value="'.$client->getId().'">';
                        echo $client->getCommercialName();
                        echo '</option>';
                    }
                    else if($client->getType() == DefaultDb_Entities_User::USER_CLIENT)
                    {
                        echo '<option value="'.$client->getId().'">';
                        echo $client->getFullName().' | '.$client->getCommercialName();
                        echo '</option>';
                    }
                }                
            }
            ?>
        </select>
        <?php else:?>
        <input type="text" readonly="readonly" value="<?php echo $user->getUsername();?>" class="order">
        <?php endif;
    }

    private function printProductsDates($order, $moneda)
    {
        ?>
        <h4>Productos</h4>
        <table class="table tab-content" id="tblCompraVenta">
          <thead>
            <tr>
              <th>cantidad</th>
              <th>Producto</th>
              <th>sku</th>
              <th>Precio</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody id="tbdCuerpo">
          

           </tbody>
          <tfoot id="tbfPie">
              
          </tfoot>
        </table>
        
        <?php
    }
    
    public function fncTablaBody($order, $moneda) {
        ?>
        
        <?php
    }
    ///////////////////////////////////////////////
    public function printTipoPago() {
        ?>
        <h4>Método de Pago</h4>
        <div class="input-group">
            <select class="form-control" >
                <option value="0">Selecciona...</option>
                <option value="1">Pago Contra Entrega</option>
                <option value="2">Pago al Momento</option>
                <option value="3">Pago Fuera de Mas Distribucion</option>
            </select>
            <span class="input-group-btn">
                <a class="btn" href="x">Realizar Pago</a>
                <!--<button class="btn btn-default" type="button">Realizar Pago</button>
                -->
            </span>
        </div>
        <?php
    }

    public function printComentarios($order) {
        ?>
        <h4>Comentarios</h4>
        <textarea disabled="disabled" class="span6"><?php echo $order->getComments();?></textarea>
        <?php
    }
    public function printOrderRepord($order,$base = 12)
    {
        $comprador = $order->getSeller();
        $vendedor = $order->getBuyer();
        $baseMidle = $base / 2;
        ?>
        <div class="row">
            <div class="span<?php echo $base;?>">
            <?php $this->printOrderDatesTable($order)?>
            </div>
        </div>
        <div class="row">
            <div class="span<?php echo $baseMidle;?>" id="sellerInfo">
                <h3><span class="icon-user"></span> Vendedor</h3>
                <?php $this->_view->Users()->profileUser($comprador);?>
            </div>
            
            <div class="span<?php echo $baseMidle;?>" id="buyerInfo">
                <h3><span class="icon-user"></span> Comprador</h3>
                <?php $this->_view->Users()->profileUser($vendedor);?>
            </div>
        </div>
        <div class="row">
            <div class="span<?php echo $base;?> tab-content">
                <?php $this->printShippingDates($order,true);?>
            </div>
        </div>
        <div class="row">
            <div class="span<?php echo $base;?>">
                <?php $this->printProductsDates($order);?>
            </div>
        </div>

        <?php
    }
    public function printCreateOrder($users, $isBuyer = null)
    {
        ?>
            <div class="row-fluid">
                <div class="span4">
                    <?php $this->printUserDates(null, $isBuyer, $users); ?>
                </div>
                <div class="span4">
                    <h4></span> Datos de la orden</h4>
                    Fecha compra:
                    <input type="text" value="<?php echo date('Y-m-d'); ?>" disabled="disabled" class="order"/>

                    Estatus Pago:
                    <input type="text" value="Sin pagar" disabled="disabled" class="order"/>

                    Estatus recolecci&oacute;n:
                    <input type="text" value="En espera" disabled="disabled" class="order" />
                </div>
                <div class="span4">
                    <h4>Comentarios:</h4>
                    <textarea name="comments" class="textarea" class="order"></textarea>
                </div>
                <div class="row">
                </div>
            </div>
        <!--El valor del checkbox se pone en cero para que inicialmente no este visible-->
         <div class="alert">
            Todas Las ordenes que se agreguen de la fecha a tres dias no se pueden cancelar
        </div>
        <input type="checkbox" value="0" name="orderR" id="orderR"/> &nbsp; &nbsp; Genera ordenes Recurrentes
        <div id="envioR"></div>
                    
        <?php
    }
    public function printSearchClients()
    {
//        foreach ( $this->_view->favoriteUsersClient as $favorite )
//        {var_dump($favorite);die;}
        ?>
            <input type="hidden" name="div_selected" id="div_selected" value="">
                <div id="div_radio_favorites">
                    <?php if ( is_array($this->_view->favoriteUsersClient) && count($this->_view->favoriteUsersClient) > 0 ): ?>
                    <span>Favoritos:</span>
                    <select class="order" name="client_favorites_id">
                        <?php foreach ( $this->_view->favoriteUsersClient as $favorite )
                        {
                            echo '<option value="' . $favorite->getFavoriteClient()->getId() . '">' . $favorite->getFavoriteClient()->getFullName() . ' | ' . $favorite->getFavoriteClient()->getCommercialName().'</option>';
                        }
                        ?>
                    </select>
                    <?php endif;?>
                </div>

                <?php if ( is_array($this->_view->categories)): ?>
                <div id="div_radio_point_fletes">
                    <span>Categoria:</span>
                    <select id="select_category" class="order">
                        <?php foreach ( $this->_view->categories as $category )
                        {
                            echo '<option value="' . $category->getId() . '">' . $category->getName() . '</option>';
                        }
                        ?>
                    </select>
                    <div id="div_points"></div>
                </div>
                <?php endif;?>
            <?php if ( !$this->_view->isBuyer ): ?>
                <div id="div_radio_clients" >
                    <span>Ciudad:</span>
                    <select id="select_state" class="order">
                        <?php foreach ( $this->_view->states as $state )
                        {
                            $selected = ($state->getId() == 24)?'selected="selected"':'';
                            echo '<option value="' . $state->getId() . '" ' . $selected . '>' . $state->getName() . '</option>';
                        }
                        ?>
                    </select>
                    <span>Cliente &oacute; Direcci&oacute;n:</span>
                    <input name="usernameTxt" class="order search-query" id="usernameTxt" autocomplete="off" type="text" class="input300" placeholder="Buscar"/><span></span>
                    <input name="hiddenBranch" type="hidden" id="hiddenBranch" />
                    <div style="position: relative;">
                        <div class="suggestionUsers-sf" id="suggestedUsers">
                            <div id="suggestedUsersList">nn</div>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        <?php
    }
    
    public function getRadioButtonsOptions($option = 1)
    {
        $radios = '';
        $radios .= '<div class="radios">';
        $checked = '';
        if($option == 1)
        {
            if (is_array($this->_view->favoriteUsersClient) && count($this->_view->favoriteUsersClient) > 0 )
            {
                $radios .= '<label class="radio"><input id="radio_favorites" type="radio" class="radio" name="radio" value="1" checked/>Clientes Favoritos</label>';
            }
            else
                $checked = 'checked';
            if ( !$this->_view->isBuyer )
                $radios .= '<label class="radio"><input id="radio_point_fletes" type="radio" class="radio" name="radio" value="2" ' . $checked . '/>Enviar a una direcci&oacute;n</label>';
            if(!$this->_view->isBuyer || count($this->_view->favoriteUsersClient) > 0 )
                $radios .= '<label class="radio"><input id="radio_clients" type="radio" class="radio" name="radio" value="3"/>Enviar a un cliente registrado</label>';
        }
        else
        {
            $radios .= '<div class="btn-group" data-toggle="buttons-radio">';
            if (is_array($this->_view->favoriteUsersClient) && count($this->_view->favoriteUsersClient) > 0 )
            {
                $radios .= '<button type="button" id="radio_favorites" class="radio btn active">Clientes Favoritos</button>';
            }
            else if (is_array($this->_view->favoriteUsersClient) && count($this->_view->favoriteUsersClient) > 0 )
                $checked = 'active';
            if ( !$this->_view->isBuyer )
                $radios .= '<button type="button" id="radio_point_fletes" class="radio btn ' . $checked . '">Enviar a una direcci&oacute;n</button>';
            if(!$this->_view->isBuyer || count($this->_view->favoriteUsersClient) > 0 )
                $radios .= '<button type="button" id="radio_clients" class="radio btn ">Enviar a un cliente registrado</button>';
            $radios .= '</div>';
        }
        $radios .= '</div>';
        return $radios ;
    }
    
   
}
