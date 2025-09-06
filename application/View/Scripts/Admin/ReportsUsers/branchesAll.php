<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Catálogos</li>
        <li class="active actualpg">Sucursales por Aprobar</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <?php
                if ($view->branchesPending)
                {
                    echo '<div class="alert">';
                    echo 'Existen ' . count($view->branchesPending) . ' Usuarios con sucursales aprovadas </div>';
                    echo '<table class="table">
                        <tr>                            <th>Estatus</th>
                            <th>ID</th>
                            <th>Nombre</th>
        					<th>Tel&eacute;fono</th>
        					<th>Direcci&oacute;n</th>
                            <th>Empresa</th>
                            <th>Reporte</th>
        					<th>Dias</th>
        					<th>Registro</th>
						</tr>';
                    foreach ($view->branchesPending as $bP)
                    {						switch($bP->getClient()->getStatus()){													case '0':								$status = 'Inactivo';								$color = 'red';								break;													case '2':								$status = 'Bloqueado';								$color = 'red';								break;																default:								$status = 'Activo';								$color = 'green';						}							                    echo '<tr>
                            <td><span style="color:'.$color.';">'.$status.'</span></td><td>'.$bP->getClient()->getId().'</td>                            <td>'.$bP->getClient()->getFullName().'</td> <td>'.$bP->getClient()->getCellPhone().'</td><td style="max-width:8em;">'.$bP->getDirection().'</td>
                            <td>'.$bP->getClient()->getCommercialName().'</td>
                            <td><a href="'.$view->getBaseUrl().'/Admin/ReportsUsers/index/id/'.$bP->getClient()->getId().'/bP/1/all/1" >Reporte</a></td>';
                    
           															$list = "";
           															 
           															if($bP->getPoint()){
           																$lista = $view->routepoint->createQueryBuilder('l')
           																->where("l.point = :point")
           																->setParameter('point',$bP->getPoint()->getId())
           																->orderBy("l.route","DESC")
           																->groupBy("l.route")
           																->getQuery()
           																->getResult();
           															
           																foreach ($lista as $route){
           																	//$list .= '<span style="color:green">Ruta: </span>'.$route->getRoute()->getName()."<br />";
           															
           																	$listaschedule = $view->schedules->createQueryBuilder('l')
           																	->where("l.route = :route")
           																	->setParameter('route',$route->getRoute()->getId())
           																	->orderBy("l.id","DESC")
           																	->groupBy("l.route")
           																	->getQuery()
           																	->getResult();
           																	 
           																	$listday = "";
           																	foreach ($listaschedule as $days){
           															
           																		$listday .= ($days->getMonday()==1)?"&nbsp;&nbsp;&bull;Lunes":"";
           																		$listday .= ($days->getTuesday()==1)?"&nbsp;&nbsp;&bull;Martes":"";
           																		$listday .= ($days->getWednesday()==1)?"&nbsp;&nbsp;&bull;Mi&eacute;rcoles":"";
           																		$listday .= ($days->getThursday()==1)?"&nbsp;&nbsp;&bull;Jueves":"";
           																		$listday .= ($days->getFriday()==1)?"&nbsp;&nbsp;&bull;Viernes":"";
           																		$listday .= ($days->getSaturday()==1)?"&nbsp;&nbsp;&bull;S&aacute;bado":"";
           																		$listday .= ($days->getSunday()==1)?"&nbsp;&nbsp;&bull;Domingo":"";
           																	}
           																	$list .= $listday."<br />";
           																}
           															}
           							echo '<td>'.$list.'</td>';
           							echo '<td>'.$bP->getClient()->getRegistration().'</td></tr>';
           															
                    
                    }
                    echo '</table>';
                }
                else
                    echo '<div class="alert"> Por el momento no hay nuevas solicitudes de sucursales por asignar  </div>';
                ?>
            </div>
        <!--</div>-->
    </div>
</div>
