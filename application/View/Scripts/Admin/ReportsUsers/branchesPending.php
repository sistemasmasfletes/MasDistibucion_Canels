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
                    echo 'Existen ' . count($view->branchesPending) . ' nuevas solicitudes de sucursales para asignarlas </div>';
                    echo '<table class="table">
                        <tr>                            <th>Estatus</th>
                            <th>ID</th>
        					<th>Nombre</th>
                            <th>Empresa</th>
                            <th>Reporte</th>
                        </tr>';
                    foreach ($view->branchesPending as $bP)
                    {						switch($bP->getClient()->getStatus()){													case '0':								$status = 'Inactivo';								$color = 'red';								break;													case '2':								$status = 'Bloqueado';								$color = 'red';								break;																default:								$status = 'Activo';								$color = 'green';						}							                    echo '<tr>
                            <td><span style="color:'.$color.';">'.$status.'</span></td><td>'.$bP->getClient()->getId().'</td>                            <td>'.$bP->getClient()->getFullName().'</td>
                            <td>'.$bP->getClient()->getCommercialName().'</td>
                            <td><a href="'.$view->getBaseUrl().'/Admin/ReportsUsers/index/id/'.$bP->getClient()->getId().'/bP/1/" >Reporte</a></td>                            </tr>';
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
