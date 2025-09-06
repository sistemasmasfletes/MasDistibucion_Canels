<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('table.display').DataTable({
        dom: 'Bfrtip',
        pagingType: "full_numbers",
        ordering: true,
        info:false,
        searching: false,
    });
} );
</script>
<script type="text/javascript">
var firstlog = <?php echo $view->fistlog; ?>
</script>
<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Panel</li>
    <li class="active actualpg ">Sucursales</li>
</ol>
</div>
<!--  Login form -->
<div class="modal hide fade in" id="loginForm" tabindex="-1" aria-hidden="false">
 	<div class="modal-dialog" role="document">
        <div class="modal-content">
        	<div class="modal-header">
              	<div class="modal-headertitle">VER VIDEO TUTORIAL</div>
					<div style="display: flex; width100%; justify-content:center; align-items: center;	">
						<button class="allbuttons" style=" background-color:#d94e19; border: none;" onclick="showvideo()">Aceptar</button>
						<!--button class="btn btn-success redonded" onclick="showvideo()">Aceptar</button-->
						<button data-dismiss="modal" aria-hidden="false" onclick="hidevideo()" class="allbuttons" style="background-color:#000; border: none;  margin-left: auto;">Omitir</button>
						<!--button data-dismiss="modal" aria-hidden="false"  onclick="hidevideo()" class="btn btn-danger redonded" style="float: right;">Omitir</button-->
					</div>
				</div>
            <!--Modal Body-->
            <div class="modal-body">
            	<div id="player" style="display:none;"></div>
            	<!-- iframe id="video1" src="https://www.youtube.com/embed/cv2zQFkrumo" frameborder="0"></iframe><br /-->	
            </div>
            <!--/Modal Body-->
    	</div>
	</div>
</div>
<!--  /Login form -->    
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <?php 
            //echo $view->is_mobile;
            //echo '<a class="allbuttons btngreen btnleft" href="' . $view->url(array('action' => 'add')) . '" ><img src="'.$view->getBaseUrl('/images/iconos/login-tienda.png').'" style="margin-right:1em;" />Agregar Sucursal</a>'; 
    		echo '<a class="allbuttons btngreen btnright" href="https://masdistribucion.com/public/app/MasDistribucion1.4.apk" target="_blank">Descarga la app <img src="'.$view->getBaseUrl('/images/iconos/logo-android.png').'" style="margin-left:1em;" /></a>';
    		?>
        <br />
        
            <div class="blockInner" style="margin-top:1.5em;">
                <h1> Direcciones de Entrega y Recolecci&oacute;n: <br /> <?php echo $view->credentials['firstName']." ".$view->credentials['lastName']; ?></h1>
            <?php
            echo '<div style="padding-bottom:1.5em;margin:1.5em;">';
            /*echo '<button class="allbuttons buttonhref"
             onclick="new_window(this.name)" name="'.
            $view->url(array('controller' => 'Store', 'action' => 'index')).
            '"><img src="'.$view->getBaseUrl('/images/iconos/storewhite.png').'" style="margin-right:1em;" />
            Tiendas</button>';
            
            echo '<button class="allbuttons buttonhref"
            onclick="new_window(this.name)" name="'.
            $view->url(array('controller' => 'FavoriteUsers', 'action' => 'index')).
            '"><img src="'.$view->getBaseUrl('/images/iconos/starwhite.png').'" style="margin-right:1em;" />
            Favoritos</button>';
            */
            /*echo '<button class="allbuttons buttonhref"
                      onclick="new_window(this.name)" name="'.
                                  $view->url(array('controller' => 'FavoriteUsers', 'action' => 'index')).
                                  '"><img src="'.$view->getBaseUrl('/images/iconos/starwhite.png').'" style="margin-right:1em;" />
                 	Tiendas Productos</button>';*/
            
            
            if($view->sales > 0){
            	echo '<button class="allbuttons buttonhref"
	                      onclick="new_window(this.name)" name="'.
            	                      $view->url(array('controller' => 'BackStore', 'action' => 'orders')).
            	                      '" title="Mis ordenes"><img src="'.$view->getBaseUrl('/images/iconos/truckwhite.png').'" style="margin-right:1em;" />
	                 	Ver mis embarques</button>';
            }
            
            if($view->purchases > 0){
            	echo '<button class="allbuttons buttonhref"
	                       onclick="new_window(this.name)" name="'.
            	                       $view->url(array('controller' => 'BackStore', 'action' => 'shopping')).
            	                       '"><img src="'.$view->getBaseUrl('/images/iconos/boxwhite.png').'" style="margin-right:1em;" />
	                 	Ver mis pedidos</button>';
            }
            
            echo '<button class="allbuttons buttonhref"
                      onclick="new_window(this.name)" name="'.$view->getBaseUrl('/Index/searchProduct/').'"><img src="'.$view->getBaseUrl('/images/iconos/icon-search.png').'" style="margin-right:1em; max-width:15px;" />
                 	Buscar Productos</button>';

            echo '</div>';
            echo '<a href="'. $view->url(array('action' => 'add')) .'" >Agregar Sucursal</a><br >';
                if (is_array($view->branches) && count($view->branches) > 0){
                	echo '<div style="margin-top:0.5em; margin-bottom:1em; display: inline-block; justify-content:rigth; align-items: center; color:red;">';
                	echo '</div>';
                	echo '<div class="contable">';
                    echo '<table class="table table-striped table-bordered table-condensed tableresp" >';
                    echo '<tr>';
                    echo '<th>C&oacute;digo QR</th>';
                    echo '<th>Nombre</th>';
                    echo '<th>Direcci&oacute;n</th>';
                    echo '<th>Rutas y D&iacute;as que Corresponden</th>';
                    echo '<th>Editar</th>';
                    echo '<th>Eliminar</th>';
                    //echo '<th>QR</th>';
                    echo '</tr>';
                    
                    foreach ($view->branches as $branche){
                    	$list = "";
                    	
                    	if($branche->getPoint()){
	                    	$lista = $view->routepoint->createQueryBuilder('l')
	                    	->where("l.point = :point")
	                    	->setParameter('point',$branche->getPoint()->getId())
	                    	->orderBy("l.route","DESC")
	                    	->groupBy("l.route")
	                    	->getQuery()
	                    	->getResult();
	                    	
	                    	foreach ($lista as $route){
	                    		$list .= '<span style="color:green">Ruta: </span>'.$route->getRoute()->getName()."<br />";

	                    		$listaschedule = $view->schedules->createQueryBuilder('l')
	                    		->where("l.route = :route")
	                    		->setParameter('route',$route->getRoute()->getId())
	                    		->orderBy("l.id","DESC")
	                    		->groupBy("l.route")
	                    		->getQuery()
	                    		->getResult();
	                    		
                    			$listday = "&nbsp;&nbsp;D&iacute;as:&nbsp;&nbsp;";
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
                    	
                        echo '<tr>';
                        echo '<td>';
                        if($branche->getPoint() != NULL){
                        	
                        	$dirqr = $_SERVER['DOCUMENT_ROOT'].$view->getBaseUrl()."/qrcode/".$branche->getPoint()->getCode().".png";
                        	 
                        	if (file_exists($dirqr)) {
	                        	echo '<a target="_blank" href="' .$view->url(array('action' => 'generatePDF', 'id' => $branche->getPoint()->getId()), true).'" title="Generar c&oacute;digo QR" >';
	                        	echo '<img style="width:7em;height:7em;" src="'.$view->getBaseUrl().'/qrcode/'.$branche->getPoint()->getCode().'.png" />';
								echo '<br />Click<br />para Imprimir</a>';
                        	}else{
	                        	echo '<button class="firstqr" style="" onclick="firstQR(this.name)" name="' .$view->url(array('action' => 'generatePDF', 'id' => $branche->getPoint()->getId()), true).'"
	                        		title="Generar c&oacute;digo QR" >C&Oacute;DIGO QR <br/ >CLICK AQU&Iacute;</button>';
	                        }
                        	
                        }
                        echo '</td>';
                        echo '<td>'.$branche->getName().'</td>';
                        echo '<td style="width:15%;" >'.$branche->getDirection().'</td>';
                        $status = $branche->getPoint() ? '' : '<span style="color:red">En Espera de Asignar Ruta</span>';
                        if($branche->getPoint()){
                        	$urlmapsctrl = ($branche->getPoint()->getUrlGoogleMaps() != "")?'<a href="'. $branche->getPoint()->getUrlGoogleMaps(). '" target="_blank" title="Ubicaci&oacute;n"><span class="pers-btn icono-posicion tam-normal"></span>Ver en maps</a>' : '';
                        }else{
                        	$urlmapsctrl = "";
                        }
                        
                        echo '<td>'.$status.$list.'<br />'.$urlmapsctrl.'</td>';
                        echo '<td style="width:4%;"><a href="' . $view->url(array('action' => 'edit', 'id' => $branche->getId())) . '">Editar</a></td>';
                        echo '<td style="width:4%;"><a href="' . $view->url(array('action' => 'delete', 'id' => $branche->getId())) . '">Eliminar</a></td>';
                        /*echo '<td>';
                        if($branche->getPoint() != NULL){
                        	echo '<a class="pers-btn icono-qr icono-tam-tabla " target="_blank" href="' .$view->url(array('action' => 'generatePDF', 'id' => $branche->getPoint()->getId()), true).'" title="Generar c&oacute;digo QR" ></a>';
                        }
                        echo '</td>';*/
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '</div>';
                    
                }
                else
                {
                    echo '<div class="alert alert-block">';
                    echo 'Aun no tiene Sucursales';
                    echo '</div>';
                }
                
                echo '<div class="contable" style="padding-top:1.5em;">';
                echo '<table id="favoriteUsersSellers"  class="table table-striped table-bordered table-condensed display tableresp" cellspacing="0" width="100%" >';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Tienda</th>';
                echo '<th>Cat&aacute;logos</th>';
                echo '</tr>';
                echo '</thead>';
                foreach ($view->stores as $store) {
                    echo '<tr>';
                    echo '<td>'.$store->getClient()->getCommercialName().'</td>';
	                echo '<td><a href="' . $view->getBaseUrl() . '/User/Store/viewAllProducts/id/' . $store->getClient()->getId() . '/"><img src="'.$view->getBaseUrl('/'.$store->getClient()->getCategory()->getImagePath()).'"></a></td>';
                    //echo '<td><a href="' . $view->getBaseUrl() . '/User/Store/viewAllProducts/id/' . $store->getClient()->getId() . '/">Ir a cat&aacute;logos</a></td>';
                    echo '</tr>';
	            }
                echo '</table>';
                echo '</div>';
                
                echo '<div style="margin-top:1.5em; display: inline-block; justify-content:left; align-items: center; color:blue;">
						<div style="width: 100%; text-align:right; text-size:1.5em; margin-bottom:1em;">Videos de Ayuda</div>
	                    <a href="#loginForm" id="clickmodal" data-toggle="modal" data-placement="" title="video tutorial primera vez"><img src="'.$view->getBaseUrl('/images/iconos/logo-youtube.png').'" /></a>
	                    <a href="https://www.youtube.com/watch?v=ChAmLBBR5yE" style="margin:1em;" title="video tutorial 2"  target="_blank"><img src="'.$view->getBaseUrl('/images/iconos/logo-youtube.png').'" /></a>
	               	</div>';
                ?>
            </div><br /><br /> 
        <!--</div>-->
    </div>
</div>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.css">
<script type="text/javascript">

	function new_window(url){
		window.open(url,'_self');
	    $(".loader").fadeIn("slow");
	}
            
	function firstQR(urlqr){
		window.open(urlqr);
		setTimeout(function(){
			location.reload();
		},1500);
	}
</script>
