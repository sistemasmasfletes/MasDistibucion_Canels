<?php
$registers = $view->registers;
$routepointsByRoute = $view->routePointsByRoute;
$minutesConfig = $view->minutesConfiguration;
//HACERLO para cada schedule
$startDateRoute = $view->startDate;

if($routepointsByRoute && $registers)
{
    $cont = 0;
    foreach($routepointsByRoute as $routepoint)
    {
        echo '<div class="row-fluid">';
        echo '<div class="span3">';
        echo $routepoint->getPoint()->getName();
        echo '</div>';
        if($cont)
        {
            $arrivalTimeInterval = new DateInterval('PT' .$routepoint->getArrivalTime()->format('i') .'M');
            $startDateRoute->add($arrivalTimeInterval);
        }
        
        echo ' <div class="">Fecha: <code>' . $startDateRoute->format('Y-m-d H:i:s') .'</code></div>';
        if($registers != false)
        {
            $haveActivity = false;
            $regCount = 0;
            foreach($registers as $r)
            {
//                var_dump('R PointId '.$r->getRoutePoint()->getPoint()->getId() , 'Route PointId '.$routepoint->getPoint()->getId(),
//                         'RoutePoint Order '.$routepoint->getOrder() , 'R Order'.$r->getRoutePoint()->getOrder());
                if($r->getRoutePoint()->getPoint()->getId() == $routepoint->getPoint()->getId()
                        && $routepoint->getOrder() == $r->getRoutePoint()->getOrder())
                {
                    
                    
                    ?>
                 <?php //if($regCount==0):?>
                <div class="span14">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                             <tr>
                                <th>Tipo</th>
                                <th>Numero de Guia</th>
                                <th>Usuario</th>
                                <th>Direccion</th>
                                <th>Fecha</th>
                                <th>Contenido</th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php //endif; ?>
                        
                        <tr>
                            <td>
                                <?php 
                                if($r->getType() == DefaultDb_Entities_SequentialActivities::TYPE_RECOLECTION)
                                    {
                                        echo '<div class="alert-success">';    
                                        echo 'Recoleccion';
                                        echo '</div>';
                                    }
                                    else
                                    {
                                        echo '<div class="alert-info">';
                                        echo 'Entrega ';
                                        echo '</div>';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php echo $r->getOrder()->getId(); ?>
                            </td>                            
                            <td>
                                <?php
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
                                echo $user->getCommercialName() . $statusString;
                                ?>
                            </td>
                            <td>
                                <?php echo $r->getRoutePoint()->getPoint()->getAddress(); ?>
                            </td>
                            <td>
                                <?php echo $r->getShippingDate()->Format('Y-m-d H:i'); ?>
                            </td>
                            <td>
                                <ul>
                                <?php
                                echo '<ul>';
                                $packages = $r->getOrder()->getPackages();
                                foreach($packages as $p)
                                {
                                    $key = $p->getNamePackage();
                                    if(!isset ($total[$key]) )
                                    {
                                        $total[$key] = 0;
                                    }

                                    $total[$key] += $p->getNumPackage();
                                    $cadena = $p->getNamePackage() .' ' . $p->getNumPackage().' x ( '.$p->getHeight().'cm x '.$p->getWidth().'cm  x '.$p->getDepth().'cm '.$p->getWeight().'Kg ) ';//.$p->getNamePackage();
                                    echo '<li>'.  $cadena .'</li>';
                                }
                                ?>
                                </ul>
                            </td>
                        </tr>
                        <?php //if($regCount): ?>
                        </tbody>
                    </table>
                </div>
                <?php //endif; ?>
                    <?php 
                    $regCount++;
                    $haveActivity=true;
                }

                
            }
            if(!$haveActivity)
            {
                echo '<div class="alert-heading span2">Sin actividad.</div>';
            }
            /**
             * Sumar el tiempo que se tarda
             */
            if($cont)
            {
                $interval = new DateInterval('PT' . $minutesConfig . 'M');
                $startDateRoute->add($interval);
            }
            $cont++;
        }
        echo '</div>';
        
    }
}
else
{
        echo '<div class="alert ">No se encontraron actividades para la ruta para el día ' . $startDateRoute->format('Y-m-d') . '.</div>';
}
// <div class="alert-block alert-danger alert-heading"