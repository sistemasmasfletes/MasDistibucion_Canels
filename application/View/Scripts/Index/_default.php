<?php
if (!Model3_Auth::isAuth()) {
    ?>
<!--  Login form -->
<div class="modal hide fade in" id="loginForm" tabindex="-1" aria-hidden="false">
 	<div class="modal-dialog" role="document">
        <div class="modal-content">
        	<div class="modal-header">
              	<div class="modal-headertitle">VER VIDEO TUTORIAL</div>
				<div style="display: flex; width100%; justify-content:center; align-items: center;	">
        	       	<button class="allbuttons" style=" background-color:#d94e19; border: none;" onclick="showvideo()">Aceptar</button>
                   	<button data-dismiss="modal" aria-hidden="false" onclick="hidevideo()" class="allbuttons" style="background-color:#000; border: none;  margin-left: auto;">Omitir</button>
				</div>
				</div>
            <!--Modal Body-->
            <div class="modal-body">
            	<div id="player" style="display:none;"></div>
				<!-- iframe id="video0" src="//www.youtube.com/embed/7noWFAEOFNM?rel=0" frameborder="0"></iframe><br /-->	
            </div>
            <!--/Modal Body-->
    	</div>
	</div>
</div>
<!--  /Login form -->        
    
		<div class="inpage">
			<!section id="slide-show" style="width:100%;margin-top:0.5em;">
				<div style="font-weight: bold; font-size:1em;">Algunos productos</div>
				<div id="slider" class="sl-slider-wrapper" style="width:100%; height:15em;margin-top:0.5em;">
				
				<?php 
		    		/*foreach ($view->imgp as $img){
	
		    			foreach ($img->getImages() as $idat){
		
			            	$orientationType="horizontal";
			                $caption="";
			                $orientation="horizontal"; $rotation1="-25"; $rotation2="-25";  $scale1="2"; $scale2="2";
			                        
			                echo '
			                	<div class="sl-slider">
			                    	<div class="sl-slide item1" data-orientation="'.$orientation.'" data-slice1-rotation="'.$rotation1.'" data-slice2-rotation="'.$rotation2.'" data-slice1-scale="'.$scale1.'" data-slice2-scale="'.$scale2.'">
			                        	<div class="sl-slide-inner">
			                            	<div class="" style="width:100%;">
			                                	<img style="max-width:95%; max-height:14em" src="'.$view->getBaseUrl('/'.$idat->getPath()).'" alt="" /> 
			                                    <br /><br />
			                                    '.$caption.'
			                              	</div>
			                         	</div>
			                     	</div>
			            		</div>';
			         	}
		    		}*/
			    ?>

               	<div class="sl-slider">
                   	<div class="sl-slide item1" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
                       	<div class="sl-slide-inner">
                           	<div class="" style="width:100%;">
                               	<img loading="lazy" style="max-width:95%; max-height:14em" src="<?php echo $view->getBaseUrl('/images/banners/1.jpg'); ?>" alt="" /> 
                                <br /><br />
                           	</div>
                       	</div>
                   	</div>
           		</div>

               	<div class="sl-slider">
                   	<div class="sl-slide item1" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
                       	<div class="sl-slide-inner">
                           	<div class="" style="width:100%;">
                               	<img loading="lazy" style="max-width:95%; max-height:14em" src="<?php echo $view->getBaseUrl('/images/banners/2.jpg'); ?>" alt="" /> 
                                <br /><br />
                           	</div>
                       	</div>
                   	</div>
           		</div>				
				
               	<div class="sl-slider">
                   	<div class="sl-slide item1" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
                       	<div class="sl-slide-inner">
                           	<div class="" style="width:100%;">
                               	<img loading="lazy" style="max-width:95%; max-height:14em" src="<?php echo $view->getBaseUrl('/images/banners/3.jpg'); ?>" alt="" /> 
                                <br /><br />
                           	</div>
                       	</div>
                   	</div>
           		</div>				
				
               	<div class="sl-slider">
                   	<div class="sl-slide item1" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
                       	<div class="sl-slide-inner">
                           	<div class="" style="width:100%;">
                               	<img loading="lazy" style="max-width:95%; max-height:14em" src="<?php echo $view->getBaseUrl('/images/banners/4.jpg'); ?>" alt="" /> 
                                <br /><br />
                           	</div>
                       	</div>
                   	</div>
           		</div>				
				
               	<div class="sl-slider">
                   	<div class="sl-slide item1" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
                       	<div class="sl-slide-inner">
                           	<div class="" style="width:100%;">
                               	<img loading="lazy" style="max-width:95%; max-height:14em" src="<?php echo $view->getBaseUrl('/images/banners/5.jpg'); ?>" alt="" /> 
                                <br /><br />
                           	</div>
                       	</div>
                   	</div>
           		</div>

               	<div class="sl-slider">
                   	<div class="sl-slide item1" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
                       	<div class="sl-slide-inner">
                           	<div class="" style="width:100%;">
                               	<img loading="lazy" style="max-width:95%; max-height:14em" src="<?php echo $view->getBaseUrl('/images/banners/6.jpg'); ?>" alt="" /> 
                                <br /><br />
                           	</div>
                       	</div>
                   	</div>
           		</div>

               	<div class="sl-slider">
                   	<div class="sl-slide item1" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
                       	<div class="sl-slide-inner">
                           	<div class="" style="width:100%;">
                               	<img loading="lazy" style="max-width:95%; max-height:14em" src="<?php echo $view->getBaseUrl('/images/banners/7.jpg'); ?>" alt="" /> 
                                <br /><br />
                           	</div>
                       	</div>
                   	</div>
           		</div>

               	<div class="sl-slider">
                   	<div class="sl-slide item1" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
                       	<div class="sl-slide-inner">
                           	<div class="" style="width:100%;">
                               	<img loading="lazy" style="max-width:95%; max-height:14em" src="<?php echo $view->getBaseUrl('/images/banners/8.jpg'); ?>" alt="" /> 
                                <br /><br />
                           	</div>
                       	</div>
                   	</div>
           		</div>
				
				</div>
			</section>    
			<!-- div style="width: 98%; border-radius:5px; background-color:green; text-align:center; padding:0.5em;">
				<a style=" color:white;  margin-top:0.5em; font-size:1.2em;" href="<?php //echo $view->getBaseUrl('/Index/pantry')  ?>" >
					<img alt="" src="<?php //echo $view->getBaseUrl('/images/despensa.jpeg')?>">
				Haz la prueba</a>
			</div-->
	
			<div class="noinapp" style="width:100%; padding: 1.5em; text-align:left; margin-top:0em;">
				Matr&iacute;z San Lu&iacute;s Potos&iacute;.<br />
				<span class="spangreen">Juli&aacute;n de los Reyes 366<br /><br />
				Villa de Pozos, S.L.P., M&eacute;xico</span>
				<br /><br />
				Correo Electr&oacute;nico:<br />
				<span class="spangreen">masdistribucion.ventas@gmail.com</span><br /><br />
				Oficina:<br />
				<span class="spangreen">01 444 521 63 52<br /><br />
					 				
				www.masdistribucion.com</span><br /><br />
	  		</div>		
	  	</div>
	    <div class="inpage inpagecenter">
			<div>
				<img style="width:20px;height:20px;margin-right:0.5em;" src="<?php echo $view->getBaseUrl('/images/iconos/carorange.png'); ?>"/>
				<a  style="color: #d74710;" href="<?php echo $view->getBaseUrl('/Index/searchProduct'); ?>">
				BUSCAR PRODUCTOS
				</a>
			</div>									
			<div class="contlogo">

				<?php
				echo '<img style="" src="' . $view->getBaseUrl('/images/iconos/logo-vertical.png') . '" />'
				?>
						<form id="formLogIn" method="post" action="Index">
						<div class="capture" style="width:70%; margin-left: 2.5em; margin-right: 2.5em;">
								<span class="labelog" style="color: #d94e19; font-family:Raleway-Light;">Iniciar Sesi&oacute;n</span><br />
						
								<?php
									echo '<img style="width:1em;" src="' . $view->getBaseUrl('/images/iconos/SILUETA.png') . '" />'
									?>
								<input class="txtlog" style="border: none; " name="mail" type="text" required="true"  placeholder="Usuario"/><br>
								<?php
									echo '<img style="width:1em;" src="' . $view->getBaseUrl('/images/iconos/CANDADO.png') . '" />'
									?>
								<input class="txtlog" style="border: none;" name="pass" type="password" required="true" placeholder="Contraseña" />
								<br><br>
								<button type="submit" class="allbuttons buttoninlog" style="background-color: #d74710; border:none; font-family:Raleway-Bold">Ingresar</button>
								<a class="allbuttons buttoninlog" style="background-color: #d74710; font-family:Raleway-Bold;" href="<?php echo $view->getBaseUrl('/Index/register')  ?>" >Registrarse</a><br />                            
								<a style="color: #d94e19;font-family:Raleway-Light;" href="<?php echo $view->getBaseUrl('/Index/sendPass')  ?>" >Restablecer contrase&ntilde;a</a>
								<br>
								</div>
						</form>	    
			</div>
		</div>
		<div class="inpage">
			<iframe style="width:70%" src="https://www.youtube.com/embed/bA0iLfq-3sQ" frameborder="0"></iframe><br />	
			<?php 
	        	echo '<div style="margin-top:1.5em; display: flex; justify-content:left;align-items: center;">
						<a href="#loginForm" onclick="videoplayclick()" class="icovideo" id="clickmodal" data-toggle="modal" data-placement="" title="video tutorial primera vez"><img src="'.$view->getBaseUrl('/images/iconos/logo-youtube.png').'" /></a>
						<a href="https://www.youtube.com/embed/7noWFAEOFNM" class="icovideo" title="video tutorial compras en l&iacute;nea" target="_blank"><img src="'.$view->getBaseUrl('/images/iconos/logo-youtube.png').'" /></a>
						<a href="https://www.youtube.com/embed/X-VUtO2pqeA" class="icovideo" title="video tutorial 2"  target="_blank"><img src="'.$view->getBaseUrl('/images/iconos/logo-youtube.png').'" /></a>
	            	  </div>';
	        ?>
			<br />
		</div>	    
	</div>
	
    <?php
	
  	echo '<link href="'.$view->getBaseUrl('/slider/css/sl-slide.css').'" rel="stylesheet">';
    echo '<link href="'.$view->getBaseUrl('/slider/css/font-awesome.min.css').'" rel="stylesheet">';

    echo '<script src="'.$view->getBaseUrl('/slider/js/modernizr-2.6.2-respond-1.1.0.min.js').'"></script>';
    echo '<script src="'.$view->getBaseUrl('/slider/js/translate.js').'"></script>';
    echo '<script src="'.$view->getBaseUrl('/slider/js/jquery.ba-cond.min.js').'"></script>';
    echo '<script src="'.$view->getBaseUrl('/slider/js/jquery.slitslider.js').'"></script>';
    
}else{
    ?>
	<div class="container">
        <div class="row" >
            <div class="span4 blockGray">
                <div class="blockInner">
                    <p>Bienvenido</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>