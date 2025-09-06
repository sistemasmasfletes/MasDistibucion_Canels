<?php 
    $_SESSION["idcomercio"] = $view->user->getId();
?>
<div class="contiene-bread" id="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Clientes</li>
        <li class="active "> <?php
            echo '<a href="' . $view->linkTo("/User/FavoriteUsers/") . '" >Comercios</a>';
            ?></li>
        <li class="active actualpg"><?php echo $view->user->getCommercialName() ? $view->user->getCommercialName() : $view->user->getFirstName() . ' ' . $view->user->getLastName(); ?></li>
    </ol>
</div>
<!--  Login form -->
<div class="modal hide fade in" id="loginForm" tabindex="-1" aria-hidden="false">
 	<div class="modal-dialog" role="document">
        <div class="modal-content">
        	<div class="modal-header">
              	<div class="modal-headertitle">VER VIDEO TUTORIAL</div>
			</div>
            <!--Modal Body-->
            <div class="modal-body">
            </div>
            <!--/Modal Body-->
    	</div>
	</div>
</div>
<!--  /Login form --> 

<div class="container">
	<?php 
	/*echo '<a class="btn-default sin-padding " href="'.$view->url(array('module'=>'User','controller'=>'Store','action'=>'user','id'=>$view->user->getId())).'" title="Volver a tienda">';
    echo '<span style="float:right;background-color:#08A4DA;width:30%;border-radius:5%;text-align:center;color:#fff;padding:.5em; box-shadow:-19px 12px 17px -7px rgba(13,12,12,0.58);">
          << TIENDA: '.$view->user->getCommercialName().'</span></a><br /<br /<br />';*/
    ?>
	<div class="infostore">
		Contactanos:<br> 
    	<?php echo $view->user->getPhone() . ', ' . $view->user->getCellPhone().'<br >'.$view->user->getMail(); ?><br />            
		<div class="contentsearch">

			<?php 
				if($view->user->getMsg2()!== NULL){ 
					if($view->user->getMsg2()!== ""){					
					?>
						<div style="margin-right:1rem;">
							<span style="font-weight:bold;color:black;">Mensaje:</span><br /><span style="color: blue;"><?php echo $view->user->getMsg2(); ?></span>
						</div>						
			<?php 
					}
				}

				if($view->user->getMsg()!== NULL){ 
					if($view->user->getMsg()!== ""){
			?>
						<div>
							<span style="font-weight:bold;color:black;">Condiciones:</span><br /><span style="color: red;"><?php echo $view->user->getMsg(); ?></span>  <br />         
							<div style="text-align: left;padding:0.5em; color:black;">Aceptar condiciones<input type="checkbox" id="condicion" value="1" style="margin:1.5em;"></div>
						</div>
			<?php  
					} 
				}
				
				if($view->user->getLink()!== NULL){ 
					if($view->user->getLink()!== ""){					
					?>
						<div style="margin-right:1rem;">
							<span style="font-weight:bold;color:black;">Enlace:</span><br /><a href="<?php echo $view->user->getLink(); ?>" style="color: blue;">Ver</a>
						</div>						
			<?php 
					}
				}
				
			?>
		</div>

   	</div>
    <div class="row" >
        <div class="blockInner">                
			<div class="contcatalog">
		    	<?php 
				 	foreach ($view->catalogs as $catalog){
				 		$div = '<button class="buttoncat" value="'.$catalog->getId().'">'.$catalog->getTitle().'</button>';
				 		echo $div;
				 	}
		 		?>
	 		</div>
	        <a href="#loginForm" id="clickvisual" data-toggle="modal" style="display: none;"></a>
			<div class="viewlist" style="display: none; min-width: 98% !important">
			</div>
			<div class="form3" style="width: 80%; display:flex; min-height: 6em !important;">
				<div class="listpantry" style="min-width:60% !important;">
					<div id="patrylist" style="text-align: left;">
						<div style="background-color:#000;color:#FFF; border-radius:5px; ">Llevas <span style="margin-right:0.5em;" id="totalist">0</span> Productos<br /></div>
					</div>
		  		</div>
		  		<div style="min-width:40%; max-width:40%; margin-left:0.5em;">		
		    		<button type="submit" class="allbuttons buttoninlog btngreen" style="display:none;" id="addallcar">Agregar a carrito</button>
		    		<span id="messages"></span>
		    		<a href="<?php echo $view->linkTo("/User/Store/viewCart/") ?>" class="allbuttons buttoninlog btngreen" style="background-color:blue; max-width:75%; display:none;" id="viewcar">Ver carrito</a>
		    	</div>
		  	</div>
        </div>
    </div>
</div>
<script type="text/javascript">
var cont = 0;
var arrayprod = [];

$(document).ready(function() {

	if ( document.getElementById( "condicion" )) {
		$("#condicion").click(function(){
			if($(this).is(':checked') ) {
				$(".buttoncat").prop("disabled",false);
			}
			$(this).prop("disabled",true);
		})
	}	
	
	$(".boxcheck").prop( "checked", false );
	
	
	$("#listok").click(function(){
		/*if(cont != 10){
			alert("Tu despensa no tiene 10 produtos");
			 return false;
		}*/
		$(".viewlist").hide();
		$(".message").show();
		$("#submit2").show();
   		$('.message').html('');
		$('.message').html('<div style="background-color:#27BAFA; border-radius:5px; font-size:1em;padding:0.5em;color:white;">POR FAVOR CAPTURA TU INFORMACI&Oacute;N DE CONTACTO, SI YA TIENES USUARIO DA CLICK <a href="#" id="setusr" style="color:green;" onclick="userexit()"> AQU&Iacute;</a></div>');
		$(this).hide();
		$(".form1").show();
		$(".form2").show();
	});

	$('.buttoncat').click(function(){

		if ( document.getElementById( "condicion" )) {
			if($("#condicion").prop('checked') == false) {
				alert("Debe aceptar las condiciones")
				return false;
			}
		}
		
		$('.viewlist').fadeOut( 100 );
        $.post(urlcatalogall,{dat: $(this).val(), arrayp: arrayprod}, 
       		function(res){
        		var res1 = res.split("--");
        		$('.viewlist').html('');
        		$('.viewlist').html(res1[0]);
	       		$('.viewlist').fadeIn( 2000 );
	       		//$('.viewlist').show();
	       		$(".imgprod").on("click",checkimg);
	       		$("input[type='checkbox']").on("click",checkboxfn);
        });
	})

	$('#addallcar').click(function(){

		var sfVariantId=null;
        var sfItemId=null;
		$.ajax({
            url: urlAddToCart,
            type: "POST",
            data: {
            	arrayp: arrayprod,
            },
            success: function(data){
                if(data == false){
                    $('#messages').append('<div class="alert alert-error">Existen productos de otra Tienda en su carrito, termine el proceso de compra o vacie el carrito.</div>');
                    $('#viewcar').show();
               	}else{
                    $('#messages').append('<div class="alert alert-success">Se a&ntilde;adieron los productos al carrito</div>');
                    window.location.href = "<?php echo $view->linkTo("/User/Store/viewCart/") ?>";
                }
            }
		});
	})	       
});	       
	
function checkboxfn(){
//$("input[type='checkbox']").click(function(){
	var names = $(this).val();
	$( '#totalist').html('');
	if( $(this).is(':checked') ) {
	    $( '<div id="'+names+'">&bull;'+$(this).attr("title")+'</div>' ).appendTo($("#patrylist"));
		arrayprod.push(names);
		$('#addallcar').fadeIn('slow');
		//$('#addallcar').show();
        cont++
	}else{
		var pos = arrayprod.indexOf(names);
		arrayprod.splice(pos, 1);
		$('#'+names).remove();
        cont--
        if(cont == 0){$('#addallcar').hide();}
	  	$("#listok").hide();
	    $(".boxcheck:not(:checked)").prop( "disabled", false );
		$(".form2").hide();
		$(".form1").hide();
		$(".message").hide();
		$("#submit2").hide();
    	$('.message').html('');
		$(".form4").hide();
    	$("#submit1").hide();
    }
	$( '#totalist').html(cont);
	//})
}

function checkimg(){
	//alert("clickimagen")
	document.getElementById("clickvisual").click();
	$(".modal-body").html("");
	$( '<img src="'+$(this).attr('src')+'" />' ).appendTo($(".modal-body"));
	
//$(".imgprod").click(function(){
	var idck = $(this).attr('id').split('img');
	var checkb = $('#bck'+idck[1])
	var names = checkb.val();
	$( '#totalist').html('');
	//if(cont < 10){
		if( checkb.is(':checked') ) {
			checkb.attr('checked', false)
			var pos = arrayprod.indexOf(names);
			arrayprod.splice(pos, 1);
			$('#'+names).remove();
	        cont--
	        if(cont == 0){$('#addallcar').hide();}
	        $("#listok").hide();
		    $(".boxcheck:not(:checked)").prop( "disabled", false );
			$(".form2").hide();
			$(".form1").hide();
			$(".message").hide();
			$("#submit2").hide();
	   		$('.message').html('');
			$(".form4").hide();
	   		$("#submit1").hide();
		}else{
			checkb.attr('checked', true)
			$( '<div id="'+names+'">&bull;'+checkb.attr("title")+'</div>' ).appendTo($("#patrylist"));
			arrayprod.push(names);
			$('#addallcar').fadeIn('slow');
			//$('#addallcar').show();
			cont++;
			/*if(cont == 10){
		    	//$(".boxcheck:not(:checked)").prop( "disabled", true );
		    	$("#listok").css("display","inline-block");
			}else{
		    	$("#listok").hide();
			}*/
		}
	/*}else{
		if(checkb.attr('disabled') !== 'disabled'){
			checkb.attr('checked', false)
			var pos = arrayprod.indexOf(names);
			arrayprod.splice(pos, 1);
			$('#'+names).remove();
	        cont--
	    	$("#listok").hide();
		    $(".boxcheck:not(:checked)").prop( "disabled", false );
			$(".form2").hide();
		    $(".form1").hide();
			$(".message").hide();
			$("#submit2").hide();
			$(".form4").hide();
	   		$('.message').html('');
	   		$("#submit1").hide();
		}
	}*/
	$( '#totalist').html(cont);
	//})
}

</script>

