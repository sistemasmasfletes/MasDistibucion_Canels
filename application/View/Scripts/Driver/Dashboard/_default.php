<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                Bienvenido Conductor <?php 
                    if($view->user instanceof DefaultDb_Entities_User)
                    {
                        echo ': '.$view->user->getFullName(); 
                    }
                ?>
            </div>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="span12 blockGray">
            <div class="blockInner">
                <form method="post" action="<?php echo $view->url(array('controller'=>'Dashboard', 'action'=>'selectReasonClient','module' => 'Driver')); ?>">
                    <label for="clientCode">Clave del Cliente</label>
                    <input type="text" name="clientCode"/>
                    <input type="submit" value="Enviar"/>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="span12 blockGray">
            <?php 
           $registers = $view->registers;
           if(is_array($registers) && count($registers) > 0):
               ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Numero de Guia</th>
                        <th>Usuario</th>
                        <th>Direccion</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Contenido</th>
                        <th>Visita</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $total = array();
                
                foreach($registers as $r)
                {
                    echo '<tr>';
                    echo '<td>'.$r->getOrder()->getId().'</td>';
                    $user = $r->getType() == DefaultDb_Entities_SequentialActivities::TYPE_RECOLECTION ? $r->getOrder()->getSeller() : $r->getOrder()->getBuyer();
                    $statusString='';
                    switch ($user->getStatus())
                    {
                        case DefaultDb_Entities_User::STATUS_INACTIVE:
                            $statusString = ' <code>Inactivo</code>';
                            break;
                        case DefaultDb_Entities_User::STATUS_BLOCK:
                            $statusString = ' <code>Bloqueado</code>';
                            break;
                    }
                    echo '<td>'.$user->getCommercialName().$statusString.'</td>';
                    echo '<td>'.$r->getRoutePoint()->getPoint()->getAddress().'</td>';
                    echo '<td>';//.$r->getOrder()->getSeller()->getCode();
                    /*@todo: Generar un helper para lo siguiente*/
                    if($r->getType() == DefaultDb_Entities_SequentialActivities::TYPE_RECOLECTION)
                    {
                        if($r->getRoutePoint()->getPoint()->getId() == $r->getOrder()->getSeller()->getPoint()->getId() ||$r->getRoutePoint()->getPoint()->getType()!=DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER)
                        {
                            //Marcar si ya fue recolectado
                            if($r->getOrder()->getShippingStatus()==DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED) //no ah sido recolectado
                            {
                                echo '<form method="POST" action="'.$view->url(array('module'=>'Driver', 'controller'=>'Dashboard', 'action'=>'redirectRecolection')).'">';
                                echo '<input type="hidden" name="clientCode" value="'.$r->getOrder()->getSeller()->getCode().'">';
                                echo '<button class="btn" type="submit" >Recolecci&oacute;n</button>';
                                echo '</form>';
                            }
                            else
                            {
                                echo '<code>'.$r->getOrder()->getShippingStatusString().'</code>';
                            }
                        }
                        else
                        {
                            if($r->getOrder()->getShippingStatus()==DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_IN_INTERCHANGE_CENTER)
                            {
                                echo '<form method="POST" action="'.$view->url(array('module'=>'Driver', 'controller'=>'Dashboard', 'action'=>'index')).'">';
                                echo '<input type="hidden" name="idOrder" value="'.$r->getOrder()->getId().'">';
                                echo '<input type="hidden" name="toInterchangeCenter" value="'.DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED.'">';
                                echo '<button class="btn" type="submit" >Recolección Centro de Intercambio</button>';
                                echo '</form>';
                            }
                            else
                            {
                                echo '<code>'.$r->getOrder()->getShippingStatusString().'</code>';
                            }
                        }
                    }
                    else
                    {
                        if($r->getRoutePoint()->getPoint()->getId() == $r->getOrder()->getBuyer()->getPoint()->getId() || $r->getRoutePoint()->getPoint()->getType() != DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER)
                        {
                            //Para el caso en el que esta en ruta pero no se tiene exactamente si es en esta ruta o en otra ruta
                            if($r->getOrder()->getShippingStatus()!=DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED && 
                                    $r->getOrder()->getShippingStatus()!=DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_DELIVERED)
                            {
                                echo '<form method="POST" action="'.$view->url(array('module'=>'Driver', 'controller'=>'Dashboard', 'action'=>'redirectShipping')).'">';
                                echo '<input type="hidden" name="clientCode" value="'.$r->getOrder()->getBuyer()->getCode().'">';
                                echo '<button class="btn" type="submit" >Entrega</button>';
                                echo '</form>';
                            }
                            else 
                            {
                                echo '<code>'.$r->getOrder()->getShippingStatusString().'</code>';
                            }
                        }
                        
                        else if($r->getOrder()->getShippingStatus()==DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED && $r->getRoutePoint()->getPoint()->getType() == DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER)
                        {
                            echo '<form method="POST" action="'.$view->url(array('module'=>'Driver', 'controller'=>'Dashboard', 'action'=>'index')).'">';
                            echo '<input type="hidden" name="idOrder" value="'.$r->getOrder()->getId().'">';
                            echo '<input type="hidden" name="toInterchangeCenter" value="'.DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_IN_INTERCHANGE_CENTER.'">';
                            echo '<button class="btn" type="submit" >Entrega Centro de Intercambio</button>';
                            echo '</form>';
                        }
                        else
                        {//var_dump($r->getOrder()->getShippingStatus());
                            if($r->getOrder()->getShippingStatus()==DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_IN_INTERCHANGE_CENTER)
                                echo '<code>'.$r->getOrder()->getShippingStatusString().'</code>';
                            else if($r->getOrder()->getShippingStatus()!=DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_DELIVERED)
                            {
                                echo '<button class="btn" >Entrega Centro de Intercambio</button>';
                            }
                            else
                            {
                                 echo '<code>'.$r->getOrder()->getShippingStatusString().'</code>';
                            }
                             
                        }
                    }
                    //echo ($r->getRoutePoint()->getPoint()->getType()==DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER ? 'Centro de Intercambio' : '');
                    echo '</td>';
                    
                    //Verificar si es centro de intercambio
                    echo '<td><code>'.$r->getShippingDate()->Format('Y-m-d H:i').'</code></td>';//<td>Fecha de Recoleccion</td>
                    $packages = $r->getOrder()->getPackages();
                    echo '<td><ul>';
                    foreach($packages as $p)
                    {
                        $key = $p->getNamePackage();
                        if(!isset ($total[$key]) )
                        {
                            $total[$key] = 0;
                        }
                        $total[$key] += $p->getNumPackage();
                        $cadena = $p->getNumPackage().' x ( '.$p->getHeight().'cm x '.$p->getWidth().'cm  x '.$p->getDepth().'cm '.$p->getWeight().'Kg ) ';//.$p->getNamePackage();
                        echo '<li>'.  $cadena .'</li>';
                    }
                    echo '</ul></td>';
                    echo '</td>';
                    echo '<td>';
                    //Cambiar el valor de las tiendas sacarlos de los puntos
                    if($r->getRoutePoint()->getPoint()->getType()!=DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER)
                    {
                        echo '<form method="POST" action="'.$view->url(array('module'=>'Driver', 'controller'=>'Dashboard', 'action'=>'redirectVisit')).'">';
                        echo '<input type="hidden" name="clientCode" value="zapateria">';
                        echo '<button class="btn" type="submit" >Visita</button>';
                        echo '</form>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
            <?php else:?>
            <div class="alert alert-info">
                No tienes ruta asignada para el d&iacute;a de hoy.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>