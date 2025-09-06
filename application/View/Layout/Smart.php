<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="shortcut icon" href="<?php echo $view->getBaseUrlPublic(); ?>/images/ui/favicon.png" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Mas distribuci&oacute;n</title>
                                <?php
                                if(isset($_SESSION['isapp'])){
                                ?>
	                                <style>
									body {
										background-image: none !important;
										background-color: #fff !important;
									}
									</style>
                                <?php
                                }
        
        $view->getCssManager()->loadCssFile('bussinesscenter/bussinesscenter.css');
        $view->getCssManager()->loadCssFile('bootstrap/bootstrap.css');
        $view->getCssManager()->loadCssFile('smart/smart.css');
        $view->getCssManager()->loadCssFile('application/GeneralContentLayout.css');
        $view->getCssManager()->loadCssFile('custom.css');
        $view->getCssManager()->loadCss();
        ?>
        <!--        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />-->
        <?php $view->getCssManager()->loadCssFile('ui/jquery-ui-1.8.14.custom.css'); ?>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
        <?php
        $view->getJsManager()->addJsVar('exitLink',  json_encode($view->url(array('module'=>false ,'controller'=>'Index','action'=>'logout'))));
        $view->getJsManager()->loadJsFile('application/exit.js');
        $view->getJsManager()->loadJsFile('bootstrap/bootstrap.js');
        $view->getJsManager()->loadJsFile('application/masdistribucion.js');
        $view->getJsManager()->loadJs();
        ?>

        <script type="text/javascript" charset="utf-8">
            //tabbed forms box
            $(function () {
                var tabContainers = $('div#forms > div.innerContent'); // change div#forms to your new div id (example:div#pages) if you want to use tabs on another page or div.
                tabContainers.hide().filter(':first').show();
			
                $('ul.switcherTabs a').click(function () {
                    tabContainers.hide();
                    tabContainers.filter(this.hash).show();
                    $('ul.switcherTabs li').removeClass('selected');
                    $(this).parent().addClass('selected');
                    return false;
                }).filter(':first').click();
            });
        </script>
    </head>

    <body>
       	<?php $view->MenuHelper()->display(); ?>
        <!--<div style="background: #FFF; padding-bottom: 20px; padding-top: 20px;" >-->            
        <?php echo $layoutdata; ?>
        <!--</div>-->
        <!--<img  src="../images/Logo_1.png" alt="logo" class="pie">--> 
        <!--img  src="<?php //echo $view->getBaseUrlPublic() ."/images/Logo_1.png" ?>" alt="logo" class="pie"--> 
        <div id="footer">
            <p align="center" valign="middle">&copy; Masdistribucion.com, Servicio de entrega econ&oacute;mico en San Luis Potos&iacute;, M&eacute;xico<br/></p>
        </div>
    </body>

	<script type="text/javascript">
	
		var idvideo;
        $(document).ready(function() {

			if(typeof firstlog === 'undefined'){
				idvideo = 'QX2COyxR7cs';
		    	setTimeout(function(){
					document.getElementById("clickmodal").click();
				},1500);
			}else{
				if(firstlog === 1){
			    	setTimeout(function(){
						document.getElementById("clickmodal").click();
					},1500);
			    	idvideo = 'cv2zQFkrumo';
				}
			}

		});
	      // 2. This code loads the IFrame Player API code asynchronously.
	      var tag = document.createElement('script');

	      tag.src = "https://www.youtube.com/iframe_api";
	      var firstScriptTag = document.getElementsByTagName('script')[0];
	      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

	      // 3. This function creates an <iframe> (and YouTube player)
	      //    after the API code downloads.
	      var player;
	      function onYouTubeIframeAPIReady() {
	        player = new YT.Player('player', {
	          height: '360',
	          width: '540',
	  	      videoId: idvideo,
	          events: {
	            //'onReady': onPlayerReady,
	            'onStateChange': onPlayerStateChange
	          }
	        });
	      }

	      // 4. The API will call this function when the video player is ready.
	      function onPlayerReady(event) {
	          	event.target.playVideo();
	      }

	      // 5. The API calls this function when the player's state changes.
	      //    The function indicates that when playing a video (state=1),
	      //    the player should play for six seconds and then stop.
	      var done = false;
	      function onPlayerStateChange(event) {
	        if (event.data == YT.PlayerState.PLAYING && !done) {
	          //setTimeout(stopVideo, 6000);
	          done = true;
	        }
	      }
	      function stopVideo() {
	        player.stopVideo();
	      }

			function showvideo(){
				$("#player").show();
				player.playVideo();
			}

			function hidevideo(){
				$("#player").hide();
				player.stopVideo();
			}
		
		document.querySelector('.menu-btn-commercial').addEventListener('click', () =>{
			document.querySelector('.nav-menu-commercial').classList.toggle('show');
		});
    </script>    
</html>
