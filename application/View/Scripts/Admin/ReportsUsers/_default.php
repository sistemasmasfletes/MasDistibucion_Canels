<div class="contiene-bread">
    <ol class="breadcrumb">
    <?php    if($view->all){    	$url = $view->url(array('controller' => 'ReportsUsers', 'action' => 'branchesAll'));    	$l = "Lista de Usuarios";    }else{    	if ($view->user){    		$url = ($view->branchesPending) ? $view->url(array('controller' => 'ReportsUsers', 'action' => 'branchesPending')) : $view->url(array('controller' => 'AdminUsers', 'action' => 'index'));    	}    	$l = "Sucursales por Aprobar";    }	
    ?>    <li class="active">Cat&aacute;logos</li>    <li class="active"> <a href="<?php echo $url; ?>" ><?php echo $l; ?></a></li>    <li class="active actualpg">Reporte</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
        <div class="blockInner">
            <div></div>

            <div class="row">
                <div class="span12">
                    <?php if (!$view->user): ?>
                        <form class="form-search" id="searchUser" method="post" action="<?php echo $view->ajaxSearch; ?>">
                            <span>Usuarios</span>
                            <input type="text" name="username" >
                            <button class="btn">
                                <span class="icon-search"></span>
                                Buscar
                            </button>
                        </form>
                        <?php
                    else:
                        $url = ($view->branchesPending) ? $view->url(array('controller' => 'ReportsUsers', 'action' => 'branchesPending')) : $view->url(array('controller' => 'AdminUsers', 'action' => 'index'));
                        ?>
<!--                        <div>
                            <a class="btn" href="<?php //echo $url; ?>" >Regresar</a>
                        </div>-->
                    <?php endif; ?>
                </div>
                <br>
                <br>
            </div>
            <div class="row">
                <div class="span4">
                    <div class="table">
                        <ul class="nav nav-list">
                            <li class="active"><a href="#" id="details">Detalles</a></li>
                            <li><a href="#" id="history">Historial de ordenes</a></li>
                            <li><a href="#" id="catalogs">Ver Catalogos</a></li>
                            <li><a href="#" id="invoices">Facturacion</a></li>
                            <li><a href="#" id="block">Bloquear usuario</a></li>
                            <li><a href="#" id="branches">Sucursales de usuario</a></li>
                        </ul>
                    </div> <!-- /well -->
                    <input id="currentUser" type="hidden" value="<?php echo ( $view->user ? $view->user->getId() : ''); ?>"/>
                </div>
                <div class="span7 table-bordered"  id="tabla">
                    <div id="reportLoader" class="hide">
                        <?php echo $view->ManagerImages()->img('images/loading.gif'); ?>
                    </div>
                    <div id="reportArea">

                        <?php
                        if ($view->user):
                            $view->Users()->profileUser($view->user);
                        else :
                            ?>
                            <div class="alert">
                                Seleccione a un usuario valido par continuar
                                <button class="close" data-dismiss="alert">×</button>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
        <!--</div>-->
    </div>
</div>
<div id="modalDiv" class="modal hide span12">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3></h3>
    </div>
    <div class="modal-body">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Close</a>
    </div>
</div>