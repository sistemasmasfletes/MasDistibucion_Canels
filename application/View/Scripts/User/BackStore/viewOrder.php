<?php
    $order =  $view->order;
    $packs = $view->packs;
?>
<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Panel</li>
    <li class="active "> <?php
        $action = ($view->isBuyer != null) ? 'shopping' : 'orders';
        echo '<a href="' . $view->url(array('action' => $action)) . '" >Entregas</a>';
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
                    	echo '<div style="width:48%;float:left">Dias de recolecci&oacute;n en mi establecimiento<br />'.$alllist.'</div>';
                    	
                    	$alllist2 = "";
                    	foreach ($view->branchesclient as $branche){
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
                    	echo '<div style="width:48%;float:left;">Dias de entrega al cliente<br />'.$alllist2.'</div><br>';
                    	 
                    	
                    	?>
         </div>           	
                    <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <?php $view->Orders()->printOrder($order,TRUE);?>
            </div>
        <!--</div>-->
    </div>
</div>
<script type="text/javascript">

	var cont = 0;
	var arrayprod = [];
	var totalprice = 0;

	function addrow(){

		if($("#newproduct").val() === ""){
			return false;
		}
		
		for(var i = 0 ; i <=  arrayprod.length - 1; i++ ){
			if($("#newproduct").val() === arrayprod[i].id){
				alert("ya esta en lista");
				return false;
			}
		}
        
		arrayprod.push({id:$("#newproduct").val(), quantity:1});
		cont++;
		
		let text = $( "#newproduct option:selected" ).text().split(" - $")
		let numsel = '<select id="qt'+$("#newproduct").val()+'" style="max-width:2em;" onchange="changequantity(this.id,this.value)">'
			for(let i = 1; i <= 100; i++){
				numsel += '<option value="'+i+'">'+i+'</option>'
			}
			numsel += '</select>'
		
		totalprice =  totalprice + parseFloat(text[1]);
		$("#spantotal").text("$"+totalprice);
				
		let table = '<tr id="tr'+$("#newproduct").val()+'">';
			table +='<td>'+numsel+'</td>';
			table +='<td>'+text[0]+'</td><td id="pricep'+$("#newproduct").val()+'">$'+parseFloat(text[1])+'</td>';
			table +='<td><a href="#" id="del'+$("#newproduct").val()+'" onclick="removerow(this.id)" >Quitar</a></td></tr>';
		
		$(table).appendTo($("#addproducts"));
		
	}                    
                    	
	function removerow(id){
		let idtr = id.split("del")
		let pos = arrayprod.findIndex( subarray => subarray.id === idtr[1] );
		let a = $("#pricep"+idtr[1]).text().split("$")

		totalprice =  totalprice - parseFloat(a[1]);
		$("#spantotal").text("$"+totalprice);
		arrayprod.splice(pos, 1);
		$('#tr'+idtr[1]).remove();
		
        cont--
	}

	function changequantity(id,val){

		let idsel = id.split("qt");
		//var pos = arrayprod.indexOf(idtr[1]);
		let pos = arrayprod.findIndex( subarray => subarray.id === idsel[1] );
		arrayprod[pos].quantity = val;
		let a = $("#pricep"+idsel[1]).text().split("$")
		totalprice =  totalprice - parseFloat(a[1]);
		$("#pricep"+idsel[1]).text("$"+parseFloat(a[1]*val))
		totalprice =  totalprice + parseFloat(a[1]*val);
		$("#spantotal").text("$"+totalprice);
		
	}

	function sendrows(){
		//alert(urladdprducts)

		if(arrayprod.length === 0){
			alert("No se agregaron productos")
			return false;
		}
		
		/*for(var i = 0 ; i <= arrayprod.length - 1; i++ ){
			alert(arrayprod[i].id);
			alert(arrayprod[i].quantity);
		}*/

		$.ajax({
            url: urladdprducts ,
            type: "POST",
            dataType: "json",
            data: {
                orderid : $('#idorder').val(),
            	arrayp: arrayprod
            },
            success: function(data){
                if(data.res == "TRUE"){
					window.location.reload();
               	}else{
                   	alert("Hubo un error al guardar la informacion");
					window.location.reload();
                }
            }
		});
	}
	
</script>
