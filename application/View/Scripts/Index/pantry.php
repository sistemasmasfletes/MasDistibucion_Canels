	<form id="despensa" method="post" action="">
		<div class="viewlist">
		
		<?php 
    	foreach ($view->products as $product){
    		$div = '<span class="contimgdata">';
    		   	foreach ($product->getImages() as $idat){
					$div .= '<img loading="lazy" alt="" class="imgprod" id="img'.$product->getId().'" src="'.$view->getBaseUrl('/'.$idat->getPath()).'">';
				}
				
    			$div .= '<div style="width:80%; box-shadow:none;margin-top:1em;">
	   			<input type="checkbox" class="boxcheck" id="bck'.$product->getId().'" name="productlist[]" title="'.$product->getName().'" value="'.$product->getId().'">
	   			<span >'.$product->getName().'</span>
   				</div>';
    			 
			$div .= '</span>';
			echo $div;
		}
		?>
		</div>

		<div class="contmessage">
			<div class="message">
				<?php echo $view->message; ?>
			</div>
			<?php 
	        	/*echo '<div style="left: right; width:30%;margin-top:0.5em; display: flex; justify-content:right;align-items: center;">
						<a style=" color:white; background-color:orange; width:50%" class="allbuttons" href="'. $view->getBaseUrl('/Index/index').'" >
						<< Volver al inicio</a>
						<a href="https://www.youtube.com/embed/bA0iLfq-3sQ" style="margin:1em;" title="video tutorial compras en l&iacute;nea" target="_blank"><img src="'.$view->getBaseUrl('/images/iconos/logo-youtube.png').'" /></a>
						<a href="https://www.youtube.com/embed/7noWFAEOFNM" style="margin:1em;" title="video tutorial compras en l&iacute;nea" target="_blank"><img src="'.$view->getBaseUrl('/images/iconos/logo-youtube.png').'" /></a>
						<a href="https://www.youtube.com/embed/X-VUtO2pqeA" style="margin:1em;" title="video tutorial 2"  target="_blank"><img src="'.$view->getBaseUrl('/images/iconos/logo-youtube.png').'" /></a>
	            	  </div>';*/
	        ?>
	  	</div>
		
	  	<div class="form1" style="">
			<div class="capture" style="">
				<span class="labelog" style="">DATOS DE CONTACTO:</span><br />
                <?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-email.png') . '" />' ?>
                <input class="txtlog" name="email" required id="" type="text" placeholder="Correo Electr&oacute;nico/Usuario" /><br>
                <?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-usuario.png') . '" />' ?>
                <input class="txtlog" name="firstName" required id="" type="text" placeholder="Nombre(s)" /><br>
                <?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-usuario.png') . '" />' ?>
                <input class="txtlog" name="lastName" required id="" type="text" placeholder="Apellido(s)" /><br>
                <?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-contrasenia.png') . '" />' ?>
                <input class="txtlog" name="pass" required id="" type="password" placeholder="Contrase&ntilde;a" /><br>
                <?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-contrasenia.png') . '" />' ?>
                <input class="txtlog" name="passconfirmation" required id="" type="password" placeholder="Confirmar Contrase&ntilde;a" /><br>
                <?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-tel.png') . '" />' ?>
                <input class="txtlog" name="cellPhone" id="" type="text" placeholder="Tel&eacute;fono" /><br />
	    	</div>			
		</div>
		<div class="form2" style="">
			<div class="capture" style="">
                <span class="labelog" style="">DIRECCI&Oacute;N:</span><br />
                <input class="txtlog" name="street" id="street" required type="text" placeholder="Calle" />
               	<input class="txtlog" name="suburb" id="suburb" required type="text" placeholder="Colonia" />
                <input class="txtlog" name="number" id="number" required type="text" placeholder="N&uacute;mero" /><br />
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
                <input class="txtlog" name="username" type="text" placeholder="Correo Electr&oacute;nico/Usuario" /><br>
                <?php echo '<img src="' . $view->getBaseUrl('/images/iconos/login-contrasenia.png') . '" />' ?>
                <input class="txtlog" name="password" id="password" type="password" placeholder="Contrase&ntilde;a" /><br>
	    	</div>
		</div>
		
		<div class="form3" style="">
			<div class="listpantry" style="">
				<div id="patrylist" style="text-align: left;">
					<div style="background-color:#000;color:#FFF; border-radius:5px; ">Llevas <span style="margin-right:0.5em;" id="totalist">0</span> Productos<br /></div>
				</div>
	  		</div>
	  		<div style="width: 100%; margin-top:0.5em;">		
			<a href="#" id="listok" class="allbuttons" style="">Aceptar</a>
	  		<button type="submit" class="allbuttons btngreen buttonin" name="submit2" id="submit2" value="submit2" style="">Comprar Despensa</button>
	    	<button type="submit" class="allbuttons btngreen buttonin"  name="submit1" id="submit1" value="submit1" style="">Comprar Despensa</button>
	  		</div>
	  	</div>

		
	</form>
</div>
    
<link href="/logistik/public/bootstrap/css/sl-slide.css" rel="stylesheet">
<link href="/logistik/public/bootstrap/css/font-awesome.min.css" rel="stylesheet">

<script src="/logistik/public/js/modernizr-2.6.2-respond-1.1.0.min.js"></script>
<script src="/logistik/public/js/translate.js"></script>
<script src="/logistik/public/js/jquery.ba-cond.min.js"></script>
<script src="/logistik/public/js/jquery.slitslider.js"></script>

<script type="text/javascript">

	var cont = 0;
	$(document).ready(function() {

		$(".boxcheck").prop( "checked", false );

		$("#listok").click(function(){
			if(cont != 10){
				alert("Tu despensa no tiene 10 produtos");
				 return false;
			}
			$(".viewlist").hide();
			$(".message").show();
			$("#submit2").show();
       		$('.message').html('');
   			$('.message').html('<div style="background-color:#27BAFA; border-radius:5px; font-size:1em;padding:0.5em;color:white;">POR FAVOR CAPTURA TU INFORMACI&Oacute;N DE CONTACTO, SI YA TIENES USUARIO DA CLICK <a href="#" id="setusr" style="color:green;" onclick="userexit()"> AQU&Iacute;</a></div>');
			$(this).hide();
			$(".form1").show();
			$(".form2").show();
		});

		$("input[type='checkbox']").click(function(){
			var names = $(this).val();
			$( '#totalist').html('');
			if( $(this).is(':checked') ) {
			    $( '<div id="'+names+'">'+$(this).attr("title")+'</div>' ).appendTo($("#patrylist"));
			    cont++;
				if(cont == 10){
			    	$(".boxcheck:not(:checked)").prop( "disabled", true );
			    	$("#listok").css("display","inline-block");
				}else{
			    	$("#listok").hide();
				}
			}else{
				$('#'+names).remove();
	            cont--
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
		})

		$('#email').blur(function(){
	        $.post(urlCheckUser,{dat:$(this).val()}, 
	       		function(res){
            		var res1 = res.split("--");
	    	       	if(res1[0]=="TRUE"){
	    	       		$('.message').html('');
    	       			$('.message').html('<div style="background-color:red; border-radius:5px; font-size:1em;padding:0.5em;color:white;">&iexcl;ATENCI&Oacute;N! EL CORREO YA ESTA REGISTRADO EN MASDISTRIBUCION</div>');
	    	       	}
	        });
		})

		$("#despensa").submit(function(){
			if($("#pass").val() !== $("#passconfirmation").val()){
				alert("Los password no coinciden");
				return false;
			}
		})

		$(".imgprod").click(function(){
			var idck = $(this).attr('id').split('img');
			var checkb = $('#bck'+idck[1])
			var names = checkb.val();
			$( '#totalist').html('');
			if(cont < 10){
				if( checkb.is(':checked') ) {
					checkb.attr('checked', false)
					$('#'+names).remove();
		            cont--
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
					$( '<div id="'+names+'">'+checkb.attr("title")+'</div>' ).appendTo($("#patrylist"));
				    cont++;
					if(cont == 10){
				    	$(".boxcheck:not(:checked)").prop( "disabled", true );
				    	$("#listok").css("display","inline-block");
					}else{
				    	$("#listok").hide();
					}
				}
			}else{
				if(checkb.attr('disabled') !== 'disabled'){
					checkb.attr('checked', false)
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
			}
			$( '#totalist').html(cont);
		})

	})	       
	
		function userexit(){
		//$(".setusr").click(function(){
		//$("#setusr").click(function(){
			$(".form2").hide();
			$(".form1").hide();
			$("#submit1").show()
			$("#submit2").hide()
			$(".form4").show();
			$("form select").each(function() { this.selectedIndex = 0 });
		    $("form input[type=text], input[type=password], form textarea").each(function() { this.value = '' });

		    $("#username").prop('required',true);
		    $("#password").prop('required',true);
             
		    $("#email").prop('required',false);
		    $("#firstName").prop('required',false);
		    $("#lastName").prop('required',false);
		    $("#pass").prop('required',false);
		    $("#passconfirmation").prop('required',false);
		     
            $("#street").prop('required',false);
            $("#suburb").prop('required',false);
            $("#number").prop('required',false);
            //$("#cp").prop('required',false);
            $('.message').html('');
   			$('.message').html('<div style="background-color:#FBB71B; border-radius:5px; font-size:1em;padding:0.5em;color:white;">POR FAVOR CAPTURA TU INFORMACI&Oacute;N DE CONTACTO, SI NO TIENES USUARIO DA CLICK <a href="#" id="setusr" style="color:green;" onclick="nouserexit()"> AQU&Iacute;</a></div>');
		//});
		}

		function nouserexit(){
		//$("#capdata").click(function(){
			$("#submit2").show()
			$("#submit1").hide()
			$(".form2").show();
			$(".form1").show();
			$(".form4").hide();
			$("form select").each(function() { this.selectedIndex = 0 });
	     	$("form input[type=text], input[type=password] , form textarea").each(function() { this.value = '' });
	     	$("#email").prop('required',true);
	     	$("#firstName").prop('required',true);
	     	$("#lastName").prop('required',true);
	     	$("#pass").prop('required',true);
	     	$("#passconfirmation").prop('required',true);
            $("#street").prop('required',true);
            $("#suburb").prop('required',true);
            $("#number").prop('required',true);
            //$("#cp").prop('required',true);
		    $("#username").prop('required',false);
		    $("#password").prop('required',false);
       		$('.message').html('');
   			$('.message').html('<div style="background-color:#27BAFA; border-radius:5px; font-size:1em;padding:0.5em;color:white;">POR FAVOR CAPTURA TU INFORMACI&Oacute;N DE CONTACTO, SI YA TIENES USUARIO DA CLICK <a href="#" id="setusr" style="color:green;" onclick="userexit()"> AQU&Iacute;</a></div>');
        //});
		}
		
</script>

