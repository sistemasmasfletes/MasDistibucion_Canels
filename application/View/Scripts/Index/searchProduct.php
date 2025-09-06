	<form id="despensa" method="post" action="">
		<?php echo ($view->sellerid =="")?"":'<input  type="hidden" name="sellerid" value="'.$view->sellerid.'" >';  ?>
		<div class="contsearch">		
		<?php if($view->view == 1){/*SI SE CUMPLE SE ACTIVAN LOS FORMULARIOS PARA COMPRA DE PRODUCTOS*/ ?>
			<input type="text" name="searchproduct" placeholder="¿Que producto buscas?" style="padding:1em; margin:1em;" />
		  	<button type="submit" class="allbuttons btngreen" name="submit3" id="submit3" value="submit3" style="">Buscar</button>
		  	<button type="button" class="allbuttons btngreen" style="background-color: red; border: 1px solid red;" onclick="cancelsearch()">Volver a buscar</button>			
		<?php } ?>
		</div>

		<?php echo $view->storename; ?>

	<div class="contentsearch">

		<div class=" <?php echo ($view->view == 1)?'viewlist2':'viewlist'; ?> " >
			<div class="searchmsg">
				<?php echo $view->serchmsg;?>
			</div>
		
		<?php 
		
		if($view->view == 2){
			foreach ($view->firstproduct as $fproduct){
			
				$input = '<span class="contimgdata"  id="span'.$fproduct->getId().'">';
				$title = $fproduct->getName().' Precio: $'.$fproduct->getPrice();
				$closeinput = '</span>';
				$inputck = '<input type="checkbox" class="boxcheck" id="bck'.$fproduct->getId().'" name="productlist[]" title="'.$title.'" value="'.$fproduct->getId().'"  checked >';

				$div = $input;
			
				$img ="";
				foreach ($fproduct->getImages() as $idat){
					$img =  '<img loading="lazy" alt=""  title="'.$title.'" class="imgprod" id="img'.$fproduct->getId().'" src="'.$view->getBaseUrl('/'.$idat->getPath()).'">';
				}
			
				$img = ($img != "")?$img:'';
				$div .= $img;
			
				$div .= '<div style="width:80%; box-shadow:none;margin-top:1em;">';
				$div .= $inputck;
				$div .= '<span >'.$title.'</span>
	   			</div>';
			
				$div .= $closeinput;
				echo $div;
			}
		}
		
    	foreach ($view->products as $product){
    		
    		switch ($view->view) {
    			case 1:
    				$input ='<button type="submit" name="clientid" value="'.$product->getClient()->getId().'-'.$product->getId().'" class="contimgdata" style="background-color: rgb(178, 178, 178,0) !important;">'; 
    				$title = $product->getClient()->getCommercialName();
					$closeinput = '</button>';
    				$inputck = '';
					$labelimg = $product->getName().' Precio: $'.$product->getPrice();
					$namestore = $title; 
				break;
    			case 2:
    				$input = '<span class="contimgdata" id="span'.$product->getId().'">';
    				$title = $product->getName().' Precio: $'.$product->getPrice();
    				$closeinput = '</span>';
    				$inputck = '<input type="checkbox" class="boxcheck" id="bck'.$product->getId().'" name="productlist[]" title="'.$title.'" value="'.$product->getId().'">';
					$labelimg = $title;	
					$namestore = "";
    				break;
    		}
    		$div = $input;

    		$img ="";
    		foreach ($product->getImages() as $idat){
				$img =  '<img loading="lazy" alt=""  title="'.$title.'" class="imgprod" id="img'.$product->getId().'" src="'.$view->getBaseUrl('/'.$idat->getPath()).'">';
			}
				
			$img = ( $img != "")?$img:'';
			$div .= '<div class="labelstore">'.$namestore.'</div>';
			$div .= $img;
				
			$div .= '<div style="width:80%; box-shadow:none;margin-top:1em;">';
    		$div .= $inputck;
	   		$div .= '<span >'.$labelimg.'</span>
   			</div>';
    			 
			$div .= $closeinput;
			echo $div;
		}
		?>
		
		</div>

		<?php if($view->view == 2){/*SI SE CUMPLE SE ACTIVAN LOS FORMULARIOS PARA COMPRA DE PRODUCTOS*/ ?>
			
			<?php //condicion de usuario logeado

			if (!Model3_Auth::isAuth()){
			?>

				<div class="form1" style="">
					<div class="capture" style="">
						<span class="labelog" style="">DATOS DE CONTACTO:</span><br />
						<?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-email.png') . '" />' ?>
						<input class="txtlog" name="email" id="" type="text" placeholder="Correo Electr&oacute;nico/Usuario" /><br>
						<?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-usuario.png') . '" />' ?>
						<input class="txtlog" name="firstName" id="" type="text" placeholder="Nombre(s)" /><br>
						<?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-usuario.png') . '" />' ?>
						<input class="txtlog" name="lastName" id="" type="text" placeholder="Apellido(s)" /><br>
						<?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-contrasenia.png') . '" />' ?>
						<input class="txtlog" name="pass" id="" type="password" placeholder="Contrase&ntilde;a" /><br>
						<?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-contrasenia.png') . '" />' ?>
						<input class="txtlog" name="passconfirmation" id="" type="password" placeholder="Confirmar Contrase&ntilde;a" /><br>
						<?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-tel.png') . '" />' ?>
						<input class="txtlog" name="cellPhone" id="" type="text" placeholder="Tel&eacute;fono" /><br />
					</div>			
				</div>
				<div class="form2" style="">
					<div class="capture" style="">
						<span class="labelog" style="">DIRECCI&Oacute;N:</span><br />
						<input class="txtlog" name="street" id="street" type="text" placeholder="Calle" />
						<input class="txtlog" name="suburb" id="suburb" type="text" placeholder="Colonia" />
						<input class="txtlog" name="number" id="number" type="text" placeholder="N&uacute;mero" /><br />
						<input class="txtlog" name="inside" id="inside" type="text" placeholder="Interior(opcional)" /><br />
						<input class="txtlog" name="cp" id="cp" type="text" placeholder="C&oacute;digo Postal" /><br />
						<input class="txtlog" name="urlmaps" id="urlmaps" type="text" placeholder="Url Google Maps(opcional)" />
						<?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-location.png') . '" />' ?>
						<br />
					</div>
				</div>	    
				
				<div class="form4" style="">
					<div class="logincap" style="">
						<span class="labelog" style="">DATOS DE USUARIO</span><br />
						<?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-email.png') . '" />' ?>
						<input class="txtlog" name="username" id="" type="text" placeholder="Correo Electr&oacute;nico/Usuario" /><br>
						<?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-contrasenia.png') . '" />' ?>
						<input class="txtlog" name="password" id="" type="password" placeholder="Contrase&ntilde;a" /><br>
					</div>
				</div>

			<?php //condicion de usuario logeado
			}
			?>

			<div class="form3">
				<div class="listpantry" style="">
					<div id="patrylist" style="text-align: left;">
						<div style="background-color:#000;color:#FFF; border-radius:5px; ">Llevas <span style="margin-right:0.5em;" id="totalist">0</span> Productos por un total de: $ <span style="margin-right:0.5em;" id="totalistprice">0.00</span> + Envio <br /></div>
					</div>
				</div>
				
				<div class="f3buttons">				
				  	<?php
					if (!Model3_Auth::isAuth()){
					?>
						<a href="#" id="listok" class="allbuttons" style="">Aceptar</a>
						<button type="submit" class="allbuttons btngreen buttonin" name="submit2" id="submit2" value="submit2" style="">Comprar Productos</button>
						<button type="submit" class="allbuttons btngreen buttonin"  name="submit1" id="submit1" value="submit1" style="">Comprar Productos</button>
					<?php }else{ ?>
						<a href="#" class="allbuttons buttoninlog btngreen" id="addallcar">Agregar a carrito</a>
					<?php } ?>
					<button id="cancelok" class="allbuttons" style="border: 1px solid red;" onclick="cancelsearch()">Volver a buscar</button>
				</div>
		  	</div>
	  	<?php } ?>
	</div>

	</form>

	<?php 
		if (!Model3_Auth::isAuth()){
		?>
			<div class="contmessage">
				<div class="message">
					<?php echo $view->message; ?>
				</div>
			</div>
		<?php } ?>


<script type="text/javascript">

	var cont = 0;
	var contprice = 0;
	var arrayprod = [];

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('input[type=text]').forEach( node => node.addEventListener('keypress', e => {
        if(e.keyCode == 13) {
          e.preventDefault();
        }
      }))
    });

	$(document).ready(function() {

		//$(".boxcheck").prop( "checked", false );

		$('.boxcheck:checked').each(
			function() {
				var names = $(this).val();
				$( '#totalist').html('');
				$( '#totalistprice').html('');
				var productdata = $(this).attr('title').split("Precio: $");

				$( '<div id="'+names+'">'+$(this).attr("title")+'</div>' ).appendTo($("#patrylist"));
				cont++;
				arrayprod.push(names);
				contprice = parseFloat(contprice) + parseFloat(productdata[1]); 
				if(cont > 0){
					$("#listok").css("display","inline-block");
				}
				$( '#totalist').html(cont);
				$( '#totalistprice').html(parseFloat(contprice));						        
		    }
		);		
		
		$("#listok").click(function(){
			if(cont == 0){
				alert("Aun no seleccionas productos");
				 return false;
			}

			$("#intersearch").hide();
		    $("input[name=username]").prop('required',false);
		    $("input[name=password]").prop('required',false);
             
		    $("input[name=email]").prop('required',true);
		    $("input[name=firstName]").prop('required',true);
		    $("input[name=lastName]").prop('required',true);
		    $("input[name=pass]").prop('required',true);
		    $("input[name=passconfirmation]").prop('required',true);
		     
            $("input[name=street]").prop('required',true);
            $("input[name=suburb]").prop('required',true);
            $("input[name=number]").prop('required',true);
            
			$(".contsearch > *").hide(400);
			$(".viewlist").hide(400);
			$(".message").fadeIn(500);
			$("#submit2").fadeIn(500);
       		$('.message').html('');
   			$('.message').html('<div style="background-color:#27BAFA; border-radius:5px; font-size:1em;padding:0.5em;color:white;">POR FAVOR CAPTURA TU INFORMACI&Oacute;N DE CONTACTO, SI YA TIENES USUARIO DA CLICK <a href="#" id="setusr" style="color:green;" onclick="userexit()"> AQU&Iacute;</a></div>');
			$(this).hide(400);
			$(".form1").fadeIn(500);
			$(".form2").fadeIn(500);
		});

		$("input[name=email]").blur(function(){
	        $.post(urlCheckUser,{dat:$(this).val()}, 
	       		function(res){
            		var res1 = res.split("--");
	    	       	if(res1[0]=="TRUE"){
	    	       		$("input[name=email]").val('');
						$('.message').html('');
    	       			$('.message').html('<div style="background-color:red; border-radius:5px; font-size:1em;padding:0.5em;color:white;">&iexcl;ATENCI&Oacute;N! EL CORREO YA ESTA REGISTRADO EN MASDISTRIBUCION, SI YA TIENES USUARIO DA CLICK <a href="#" id="setusr" style="color:green;" onclick="userexit()"> AQU&Iacute;</a></div>');
    		  		}
	        });
		})

		$("input[name=passconfirmation]").blur(function(){
			if($("input[name=passconfirmation]").val() !== $("input[name=pass]").val()){
				//var msg = '<div style="background-color:red; border-radius:5px; font-size:1em;padding:0.5em;color:white;">&iexcl;ATENCI&Oacute;N! LAS CONSTRASENAS NO COINCIDEN! <a href="#" id="setusr" style="color:green;" onclick="msgacept()">Aceptar</a></div>'
				//$( msg ).insertAfter( $( ".message > div" ) );
				$('.message').html('');
	   			$('.message').html('<div style="background-color:red; border-radius:5px; font-size:1em;padding:0.5em;color:white;">&iexcl;ATENCI&Oacute;N! LAS CONSTRASENAS NO COINCIDEN! <a href="#" id="setusr" style="color:green;" onclick="msgacept()">Aceptar</a></div>');
				$(this).val('');
				$("input[name=pass]").val('');
			}
		})		
		
		$("#despensa").submit(function(){
			//if($("input[name=passconfirmation]").is(":visible") {
			//alert($("input[name=passconfirmation]").length);
				if($("input[name=passconfirmation]").length > 0) {
					if($("input[name=passconfirmation]").prop("required")){
						if($("input[name=pass]").val() !== $("input[name=passconfirmation]").val()){
							alert("Los password no coinciden");
							return false;
						}
					}
				}
			//}
		})
		
		$("input[type='checkbox']").click(function(){
			var names = $(this).val();
			$( '#totalist').html('');
			$( '#totalistprice').html('');

			var productdata = $(this).attr('title').split("Precio: $");

			if( $(this).is(':checked') ) {
			    $( '<div id="'+names+'">'+$(this).attr("title")+'</div>' ).appendTo($("#patrylist"));
			    cont++;
				arrayprod.push(names);
			    contprice = parseFloat(contprice) + parseFloat(productdata[1]); 
			    if(cont > 0){
			    	$("#listok").css("display","inline-block");
			    }
			}else{
				var pos = arrayprod.indexOf(names);
				arrayprod.splice(pos, 1);
				$('#'+names).remove();
	            cont--
			    contprice = parseFloat(contprice) - parseFloat(productdata[1]); 
	            if(cont < 1){
			    	$("#listok").hide(400);
			     }
				$(".form2").hide(400);
				$(".form1").hide(400);
				$(".message").hide(400);
				$("#submit2").hide(400);
	       		$('.message').html('');
				$(".form4").hide(400);
	       		$("#submit1").hide(400);
	       	}
	       	
			$( '#totalist').html(cont);
			$( '#totalistprice').html(parseFloat(contprice));
		})
		
		$(".imgprod").click(function(){
			var idck = $(this).attr('id').split('img');
			var checkb = $('#bck'+idck[1])
			var names = checkb.val();

			var productdata = $(this).attr('title').split("Precio: $");
			
			$( '#totalist').html('');
			$( '#totalistprice').html('');			
				if( checkb.is(':checked') ) {
					checkb.attr('checked', false)
				var pos = arrayprod.indexOf(names);
					arrayprod.splice(pos, 1);
					$('#'+names).remove();
					cont--
				    contprice = parseFloat(contprice) - parseFloat(productdata[1]); 
				    if(cont < 1){
				    	$("#listok").hide(400);
			    	}
				    $(".boxcheck:not(:checked)").prop( "disabled", false );
					$(".form2").hide(400);
					$(".form1").hide(400);
					$(".message").hide(400);
					$("#submit2").hide(400);
		       		$('.message').html('');
					$(".form4").hide(400);
		       		$("#submit1").hide(400);
					
				}else{
					checkb.attr('checked', true)
					$( '<div id="'+names+'">'+checkb.attr("title")+'</div>' ).appendTo($("#patrylist"));
				    cont++;
					arrayprod.push(names);
				    contprice = parseFloat(contprice) + parseFloat(productdata[1]); 
				    if(cont > 0){
				    	$("#listok").css("display","inline-block");
					 }
				}
			$( '#totalist').html(cont);
			$( '#totalistprice').html(parseFloat(contprice));
		})

		$('#addallcar').click(function(){

			if(arrayprod.length > 0){
				$.ajax({
					url: urlAddToCart,
					type: "POST",
					data: {
						arrayp: arrayprod,
					},
					success: function(data){
						if(data == false){
							alert("Ya tiene productos de otra tienda en su carrito")
							$('#messages').append('<div class="alert alert-error">Existen productos de otra Tienda en su carrito, termine el proceso de compra o vacie el carrito.</div>');
							$('#viewcar').show();
						}else{
							//$('#messages').append('<div class="alert alert-success">Se a&ntilde;adieron los productos al carrito</div>');
							window.location.href = "<?php echo $view->linkTo("/User/Store/viewCart/") ?>";
						}
					}
				});
			}else{
				alert("No tienes productos en tu lista")
			}
		})

		$("#intersearch").keyup(function(){
			if($(this).val().length > 2){
			var search = $(this).val().toUpperCase();
				//alert(search)
				$('.contimgdata').each(
					function() {
						//alert($(this).attr('id'))
						var names = $(this).text().toUpperCase();
						//alert(names)
						//alert(names.indexOf(search))
						if(names.indexOf(search) >= 0){
							//alert("encontrado");
							//$('.viewlist').animate({ scrollTop: $(this).offset().top }, 500);
							document.getElementById( $(this).attr('id')).scrollIntoView();
    						window.setTimeout( function () { top(); }, 2000 );
							//$(this).focus();
							return false;
						}

				});
			}
		})	       

	})	       
	
	function msgacept(){
   		$('.message').html('');
		$('.message').html('<div style="background-color:#27BAFA; border-radius:5px; font-size:1em;padding:0.5em;color:white;">POR FAVOR CAPTURA TU INFORMACI&Oacute;N DE CONTACTO, SI YA TIENES USUARIO DA CLICK <a href="#" id="setusr" style="color:green;" onclick="userexit()"> AQU&Iacute;</a></div>');
	}
	
	function userexit(){
		$(".form2").hide(400);
		$(".form1").hide(400);
		$("#submit1").fadeIn(500)
		$("#submit2").hide(400)
		$(".form4").fadeIn(500);
		$("form select").each(function() { this.selectedIndex = 0 });
		$("form input[type=text], input[type=password], form textarea").each(function() { this.value = '' });

		$("input[name=username]").prop('required',true);
		$("input[name=password]").prop('required',true);
             
		$("input[name=email]").prop('required',false);
		$("input[name=firstName]").prop('required',false);
		$("input[name=lastName]").prop('required',false);
		$("input[name=pass]").prop('required',false);
		$("input[name=passconfirmation]").prop('required',false);
		     
        $("input[name=street]").prop('required',false);
        $("input[name=suburb]").prop('required',false);
        $("input[name=number]").prop('required',false);
        $('.message').html('');
   		$('.message').html('<div style="background-color:#FBB71B; border-radius:5px; font-size:1em;padding:0.5em;color:white;">POR FAVOR CAPTURA TU INFORMACI&Oacute;N DE CONTACTO, SI NO TIENES USUARIO DA CLICK <a href="#" id="setusr" style="color:green;" onclick="nouserexit()"> AQU&Iacute;</a></div>');
	}

	function nouserexit(){
		$("#submit2").fadeIn(500)
		$("#submit1").hide(400)
		$(".form2").fadeIn(500);
		$(".form1").fadeIn(500);
		$(".form4").hide(400);
		$("form select").each(function() { this.selectedIndex = 0 });
	   	$("form input[type=text], input[type=password] , form textarea").each(function() { this.value = '' });

	    $("input[name=username]").prop('required',false);
	    $("input[name=password]").prop('required',false);
             
	    $("input[name=email]").prop('required',true);
	    $("input[name=firstName]").prop('required',true);
	    $("input[name=lastName]").prop('required',true);
	    $("input[name=pass]").prop('required',true);
	    $("input[name=passconfirmation]").prop('required',true);
		     
        $("input[name=street]").prop('required',true);
        $("input[name=suburb]").prop('required',true);
        $("input[name=number]").prop('required',true);
       		
    	$('.message').html('');
   		$('.message').html('<div style="background-color:#27BAFA; border-radius:5px; font-size:1em;padding:0.5em;color:white;">POR FAVOR CAPTURA TU INFORMACI&Oacute;N DE CONTACTO, SI YA TIENES USUARIO DA CLICK <a href="#" id="setusr" style="color:green;" onclick="userexit()"> AQU&Iacute;</a></div>');
	}
		
	function cancelsearch(e){
		if (window.history.replaceState) { // verificamos disponibilidad
			window.history.replaceState(null, null, window.location.href);
		}
		window.location.reload();		
	}
	
</script>

