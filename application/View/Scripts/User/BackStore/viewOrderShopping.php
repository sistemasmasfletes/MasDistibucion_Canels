<?php
    $order =  $view->order;
    $packs = $view->packs;
?>
<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Panel</li>
    <li class="active "> <?php
        echo '<a href="' . $view->url(array('action' => 'shopping')) . '" >Recolecciones</a>';
        ?></li>
    <li class="active actualpg ">Orden</li>
</ol>
</div>
<div class="container">
    <div class="row" >
    <div style="height: 9.5em;">
    <?php     
    				$alllist = "";
    				$lstpoints = "";
    				$c=0;
    				$tcbr = count($view->branches);
    				
                    foreach ($view->branches as $branche){
                        if($c > 0 && ($tcbr-1) < $tcbr ){$lstpoints .= ","; }
                        $lstpoints .= $branche->getPoint()->getId();
                    	$c++;
                    }

                    $list = "";
                    
                    	//if($branche->getPoint()){
	                    	$lista = $view->routepoint->createQueryBuilder('l')
	                    	->where("l.point in(".$lstpoints.")")
	                    	//->setParameter('point',$tcbr)
	                    	//->setParameter('point',$branche->getPoint()->getId())
	                    	->orderBy("l.route","ASC")
	                    	->groupBy("l.route")
	                    	->getQuery()
	                    	->getResult();

	                    	foreach ($lista as $route){
	                    		$list .= 'Ruta:'.$route->getRoute()->getName()."<br />";

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
                    	//}
                    	$alllist .= $list;
                    //}
                    	echo '<div style="width:48%;float:left">Dias de entrega en mi establecimiento<br />'.$alllist.'</div>';
                    	
                    	$alllist2 = "";
                    	foreach ($view->branchesseller as $branche){
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
                    				$list .= 'Ruta:'.$route->getRoute()->getName()."<br />";
                    	
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
                    		$alllist2 .= $list;
                    	}
                    	echo '<div style="width:48%;float:left;">Dias de recolecci&oacute;n con mi proveedor<br />'.$list.'</div><br></div>';
                    	
                    	?>    
    
    <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                    <?php $view->Orders()->printOrder($order,false);?><!-- ESTO SE OBTIENE DEL HELPER Orders.php -->
            </div>
        <!--</div>-->
    </div>
</div>

<style>
    input ,select{ display: block; }
</style>