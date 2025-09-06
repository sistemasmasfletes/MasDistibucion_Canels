<?php 
    $rp = $view->routePoint;
    /* @var $rp DefaultDb_Entities_RoutePoint */
?>
<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <?php echo '<h1>Edicion del punto '.$rp->getPoint()->getName().' de la ruta '.$rp->getRoute()->getName().'</h1>';?>
                <a class="btn" href="<?php echo $view->url(array('action' => 'points'), true);?>">Regresar a puntos de venta</a>
                <br />
                <br />
                <form action="<?php echo $view->url(array('action' => 'saveRoutePoint'));?>" method="post">
                    <table>
                        <tr>
                            <td>Clave:</td>
                        </tr>
                        <tr>
                            <td><input type="text" value="<?php echo $rp->getPoint()->getCode();?>" name="code"/></td>
                        </tr>
                        <tr>
                            <td>Nombre:</td>
                        </tr>
                        <tr>
                            <td><input type="text" value="<?php echo $rp->getPoint()->getName();?>" name="name"/></td>
                        </tr>
                        <tr>
                            <td>Tipo:</td>
                        </tr>
                        <tr>
                            <td>
                                <select name="type">
                                    <?php
                                        if($rp->getPoint()->getType() == DefaultDb_Entities_Point::TYPE_SALE_POINT)
                                            echo '<option value="'.DefaultDb_Entities_Point::TYPE_SALE_POINT.'" selected="selected">Punto de venta</option>';
                                        else
                                            echo '<option value="'.DefaultDb_Entities_Point::TYPE_SALE_POINT.'">Punto de venta</option>';
                                        if($rp->getPoint()->getType() == DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER)
                                            echo '<option value="'.DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER.'" selected="selected">Centro de intercambio</option>';
                                        else
                                            echo '<option value="'.DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER.'">Centro de intercambio</option>';
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Dirección:</td>
                        </tr>
                        <tr>
                            <td><input type="text" value="<?php echo $rp->getPoint()->getAddress();?>" name="address" /></td>
                        </tr>
                        <tr>
                            <td>Estado:</td>
                        </tr>
                        <tr>
                            <td>
                                <select name="state">
                                    <?php if ( !$rp->getPoint()->getState())
                                    {
                                        echo '<option value="0">Seleccione una opcion</option>';
                                    }
                                    ?>
                                    <?php foreach ( $view->states as $state ): ?>
                                    <?php $selected = ($rp->getPoint()->getState() && $rp->getPoint()->getState()->getId() == $state->getId()) ? 'selected="selected"' : '';
                                    ?>
                                    <option value="<?php echo $state->getId(); ?>" <?php echo $selected; ?>><?php echo $state->getName(); ?></option>
                                    <?php endforeach;?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Tiempo de llegada con respecto al punto anterior (en minutos):</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" value="<?php echo $rp->getArrivalTime()->format('i');?>" name="arrivalTime"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="submit" value="Actualizar"/>
                                <input type="hidden" value="<?php echo $rp->getId();?>" name="routePointId" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>