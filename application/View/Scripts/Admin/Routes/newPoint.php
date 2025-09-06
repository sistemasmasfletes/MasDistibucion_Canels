<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>
                    Agregar punto de venta                 
                </h1>&nbsp;&nbsp;
                <div name="alert" id="alert">
                    <div class="alert alert-success">
                        Registro modificado con exito
                    </div>
                </div>
                <div name="alert" id="alert-error">
                    <div class="alert alert-info">
                        Error al agregar datos
                    </div>
                </div>
                <h3>
                    <?php echo $view->route->getName(); ?>
                </h3>
                <a class="btn" href="<?php echo $view->url(array('action' => 'points'), true); ?>">Regresar a puntos de venta</a><br/><br/>
                <select id="salePointTypeSelector">
                    <option value="1" selected="selected">Nuevo punto de venta</option>
                    <option value="2">Punto de venta existente</option>
                    <option value="3">Centro de intercambio existente</option>
                    <option value="4">Centro de intercambio existente de otra ruta</option>
                </select>
                <div class="clear" style="height:15px;" ></div>
                <div id="newSalePointContainer">
                    <h2>Nuevo punto de venta/centro de intercambio</h2>
                    <table>
                        <tr>
                            <td>Clave</td>
                        </tr>
                        <tr>
                            <td><input type="text" id="pointCode"/></td>
                        </tr>
                        <tr>
                            <td>Nombre</td>
                        </tr>
                        <tr>
                            <td><input type="text" id="pointName"/></td>                           
                        </tr>
                        <tr>
                            <td>Tipo:</td>
                        </tr>
                        <tr>
                            <td>
                                <select id="pointType">
                                    <option value="0" selected="selected">Seleccione...</option>
                                    <option value="1">Punto de venta</option>
                                    <option value="2">Centro de intercambio</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Dirección</td>
                        </tr>
                        <tr>
                            <td><input type="text" id="pointAddress" value=""/></td>                           
                        </tr>
                        <tr>
                            <td>Estado:</td>
                        </tr>
                        <tr>
                            <td>
                                <select id="state">
                                    <?php foreach ($view->states as $state): ?>
                                        <option value="<?php echo $state->getId(); ?>"><?php echo $state->getName(); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>

                    </table>
                </div>
                <div id="existentSalePointContainer">
                    <h2>Punto de venta existente</h2>
                    <select id="existentSalePointSelector">
                        <option value="0" selected="selected">Seleccione...</option>
                        <?php
                        /* @var $sp DefaultDb_Entities_RoutePoint */
                        foreach ($view->salePoints as $sp)
                        {
                            echo '<option value="' . $sp->getId() . '">' . $sp->getName() . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div id="existentExchangePointContainer">
                    <h2>Centro de intercambio existente</h2>
                    <select id="existentExchangePointSelector">
                        <option value="0" selected="selected">Seleccione...</option>
                        <?php
                        $points = array();                        
                        
                        /* @var $ec DefaultDb_Entities_RoutePoint */
                        foreach ($view->exchangeCenters as $ec)
                        {                            
                            if(in_array($ec->getPoint()->getId(), $points))
                                continue;
                            array_push($points, $ec->getPoint()->getId());
                            
                            if ($ec->getStatus() != DefaultDb_Entities_RoutePoint::STATUS_NORMAL)
                                continue;
                            
                            echo '<option value="' . $ec->getPoint()->getId() . '">' . $ec->getPoint()->getName() . '</option>';
                            
                        }
                        ?>
                    </select>
                </div>
                <div id="allExistentExchangePointContainer">
                    <h2>Centro de intercambio existente de otra Ruta</h2>
                    <select id="allExistentExchangePointSelector">
                        <option value="0" selected="selected">Seleccione...</option>
                        <?php
                        /* @var $ec DefaultDb_Entities_RoutePoint */

                        foreach ($view->allexchangeCenters as $aec)
                        {
                            echo '<option value="' . $aec->getId() . '">' . $aec->getName() . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <table>
                    <tr>
                        <td><h3>Información con respecto a la ruta</h3></td>
                    </tr>
                    <tr>
                        <td>Tiempo con respecto al punto anterior en minutos: </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" id="routePointArrivalTime" />
                        </td>
                    </tr>
                    <tr>
                        <td><input class="btn" type="button" id="submitter" value="Agregar" /></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>