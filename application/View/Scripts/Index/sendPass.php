<!-- a class="button" href="<?php /* echo $view->url(array('controller'=>'Index','action'=>'recoverPass'))?>"><div class="smartButton">Back</div></a>
<?php if($view->res===false):?>
    <div class="errorBox ">
        <div class="msgBoxContent errorIcon">
            <?php foreach($view->errors as $error):?>
            <p> <?php echo $view->TrHelper()->_($error); ?><br></p>
            <?php endforeach; ?>
        </div>
        </div>
<?php else : ?>
    <div class="successBox ">
        <div class="msgBoxContent successIcon">
            <p><?php echo $view->TrHelper()->_('your password has been successfully sent to your email'); ?><br></p>
        </div>
        </div>
<?php endif; */ ?>
-->
<?php if($view->res===false):?>
                    <form id="formLogIn" method="post" action="" style="margin-top:1.5em; width:65%">
					<div class="capture" style="width:95%">
                    		<span class="labelog" style="">Restablecer contrase&ntilde;a</span><br />
                    
                            <?php
                                echo '<img src="' . $view->getBaseUrl('/images/iconos/login-usuario.png') . '" />'
                                ?>
                            <input class="txtlog" name="email" type="text" required="true"  placeholder="Escribe tu correo de registro"/><br>
                            <br><br>
                            <button type="submit" class="allbuttons btngreen buttoninlog">Solicitar</button>
                            <br>
                            </div>
                    </form>	  
	<?php if($view->msg !==false){?>
    	<div style="background-color:red; border-radius:5px; font-size:1em;padding:0.5em;color:white; width:65%">
    		&iexcl;ATENCI&Oacute;N, <?php echo $view->msg; ?>! 
    	</div>'
     <?php } ?>
<?php else : ?>
    	<div style="background-color:green; border-radius:5px; font-size:1em;padding:0.5em;color:white; width:65% ; margin-top:1em;">
    		&iexcl;ATENCI&Oacute;N, <?php echo $view->msg; ?>! 
    	</div>'
<?php endif;  ?>
