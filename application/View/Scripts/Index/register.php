<?php /*echo'<a class="button" href="' . $view->url(array('controller' => 'Index')) . '"><div class="smartButton">Back</div></a>'; ?><br/><br/>
<?php /* @var $view Model3_View */ ?>
<!-- script>
    var baseUrl="<?php //echo $view->getBaseUrl(); ?>";
</script>
<?php //if ($view->res === false): ?>    
    <?php //foreach ($view->errors as $error): ?>
        <div class="errorBox">
            <div class="errorBoxTop">&nbsp;</div>
            <div id="errorResult" class="msgBoxContent errorIcon">
                <p><?php //echo $view->TrHelper()->_($error); ?></p> 
            </div>
        </div>
    <?php //endforeach; ?>
<?php //endif; ?>
<?php //$view->ProfileHelper()->editSaveProfile(); ?>
<input type="hidden" id="urlValidateEmail" value="<?php //echo $view->url(array('controller'=>'Index','action'=>'AxValidateEmail')) ?>" / -->
<!--  Login form -->
<div class="modal hide fade in" id="loginForm1" tabindex="-1" aria-hidden="false">
 	<div class="modal-dialog" role="document">
        <div class="modal-content">
        	<div class="modal-header">
            <?php 
				echo '<a class="allbuttons btngreen buttoninlog" href="https://masdistribucion.com/public/app/MasDistribucion1.4.apk" target="_blank">Descarga la app<img src="'.$view->getBaseUrl('/images/iconos/logo-android.png').'" style="margin-left:1em;" /></a>';
  			?>
        	</div>
            <!--Modal Body-->
            <div id="formLogIn" class="modal-body" style="color:grey">
            	<span class="labelog">1. Descarga la app para Android<br /><br />
            	2. Asegurate de que tu dispositivo Android permita instalaci&oacute;n de origenes desconocidos<br />
            	(p&aacute;gina de referencia: <a href="https://www.vichaunter.org/como-se-hace/activar-origenes-desconocidos-cualquier-android" target="_blank">Aqu&iacute;</a>)<br /><br />
            	3. Una vez descargada la app ub&iacute;cala en tu dispositivo y seleccionala para la instalaci&oacute;n</span>
            </div>
            <!--/Modal Body-->
    	</div>
	</div>
</div>
<!--  /Login form -->                            
<div id="page">
    <div class="container" style="width: 100%;">
        <div class="centrado" style="min-width: 50%;padding:auto;">

                                <div>
                                    <?php
                                    echo $view->message;

                                    ?>
                                </div>
        
<?php if($view->success == "0") { ?>
                    <form id="formRegister" method="post" action="">
					<div class="capture" style="">

				<span class="labelog" style="">Registrarse:</span><br />
                <?php echo '<img  style="width:1em;" src="' . $view->getBaseUrl('/images/iconos/ARROBA.png') . '" />' ?>
                <input class="txtlog" name="email" required id="" type="text" placeholder="Correo Electr&oacute;nico/Usuario" /><br>
                <?php echo '<img  style="width:1em;" src="' . $view->getBaseUrl('/images/iconos/SILUETA.png') . '" />' ?>
                <input class="txtlog" name="firstName" required id="" type="text" placeholder="Nombre(s)" /><br>
                <?php echo '<img  style="width:1em;" src="' . $view->getBaseUrl('/images/iconos/SILUETA.png') . '" />' ?>
                <input class="txtlog" name="lastName" required id="" type="text" placeholder="Apellido(s)" /><br>
                <?php echo '<img  style="width:1em;" src="' . $view->getBaseUrl('/images/iconos/CANDADO.png') . '" />' ?>
                <input class="txtlog" name="pass" required id="" type="password" placeholder="Contrase&ntilde;a" /><br>
                <?php echo '<img  style="width:1em;" src="' . $view->getBaseUrl('/images/iconos/CANDADO.png') . '" />' ?>
                <input class="txtlog" name="passconfirmation" required id="" type="password" placeholder="Confirmar Contrase&ntilde;a" /><br>
                <?php echo '<img  style="width:1em;" src="' . $view->getBaseUrl('/images/iconos/TELEFONO.png') . '" />' ?>
                <input class="txtlog" name="cellPhone" id="" type="text" placeholder="Tel&eacute;fono" /><br />
                
                <div class="contradio">            
	                <input type="radio" onclick="typeselect(this.value)" name="pointype" value="1" checked >Casa Particular <br />
	                <input type="radio" onclick="typeselect(this.value)" name="pointype" value="2" >Comercio 
				</div>
				
                <span class="labelog" style="">Direcci&oacute;n:</span><br />
                <div id="comercialdiv" style="display:none;">
	                <?php
		            	echo '<img style="margin-bottom:0.5em;" src="' . $view->getBaseUrl('/images/iconos/login-tienda.png') . '" /><br />'
		            ?>
	                <select name="category" id="category"><option value="">Seleccione una opci&oacute;n</option><?php echo $view->optionlist; ?></select><br />
		            <input class="txtlog" name="commercialname" id="commercialname" type="text" placeholder="Nombre del Comercial" />
                </div>

                <input class="txtlog" name="street" id="street" required type="text" placeholder="Calle" />
               	<input class="txtlog" name="suburb" id="suburb" required type="text" placeholder="Colonia" />
                <input class="txtlog" name="number" id="number" required type="text" placeholder="N&uacute;mero" /><br />
                <input class="txtlog" name="inside" id="inside" type="text" placeholder="Interior(opcional)" /><br />
                <input class="txtlog" name="cp" id="cp" type="text" placeholder="C&oacute;digo Postal" /><br />
	            <input class="txtlog" name="urlmaps" id="urlmaps" type="text" placeholder="Url Google Maps(opcional)" />
	            <?php echo '<img   style="width:1em;" src="' . $view->getBaseUrl('/images/iconos/UBICACION.png') . '" />' ?>                            
                            
                            <button type="submit" class="allbuttons buttoninlog btngreen">Enviar</button>
                            <br>
                            <br>
                    	</div>
                    	</form>
<?php }else{ ?>                            
            <?php
            echo '<div style="width:50%;padding:auto;"><img style="" src="' . $view->getBaseUrl('/images/iconos/logo-vertical.png') . '" /></div>'
            ?>
                    <form id="formLogIn" method="post" action="Index">
					<div class="capture" style="">
                    		<span class="labelog" style="">Iniciar Sesi&oacute;n</span><br />
                    
                            <?php
                                echo '<img src="' . $view->getBaseUrl('/images/iconos/login-usuario.png') . '" />'
                                ?>
                            <input class="txtlog" name="mail" type="text" required="true"  placeholder="Usuario"/><br>
                            <?php
                                echo '<img src="' . $view->getBaseUrl('/images/iconos/login-contrasenia.png') . '" />'
                                ?>
                            <input class="txtlog" name="pass" type="password" required="true" placeholder="Contraseña" />
                            <br><br>
                            <button type="submit" class="allbuttons btngreen buttoninlog">Ingresar</button>
                            <br>
                            </div>
                    </form>
        			<a href="#loginForm1" id="clickmodal" data-toggle="modal" style="display:none;"></a>
<?php } ?>
		</div>
    </div>
</div>
<script type="text/javascript">

	setTimeout(function(){
		document.getElementById("clickmodal").click();
	},500);
	
	function typeselect(val){
		if(val==2){
			$("#comercialdiv").show();
		}else{
			$("#comercialdiv").hide();
		}
	}
</script>
    

