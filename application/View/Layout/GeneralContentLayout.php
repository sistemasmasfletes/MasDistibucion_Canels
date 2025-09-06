<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="<?php echo $view->getBaseUrlPublic(); ?>/images/ui/favicon.png" />
        <!-- librerías opcionales que activan el soporte de HTML5 para IE8 -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <title>Mas distribuci&oacute;n</title>
       
        <?php

        $view->getCssManager()->loadCssFile('bootstrap-3.2.0/bootstrap.min.css');        
        $view->getCssManager()->loadCssFile('jqgrid/blitzer/jquery-ui-1.10.3.custom.css');
        $view->getCssManager()->loadCssFile('jqgrid/ui.jqgrid.css');
        $view->getCssManager()->loadCssFile('jqgrid/jqGrid.overrides.css');
        $view->getCssManager()->loadCssFile('application/GeneralContentLayout.css');
        //$view->getCssManager()->loadCssFile('smart/smart.css');
        $view->getCssManager()->loadCss();        
        
        $view->getJsManager()->loadJsFile('jquery/jquery-1.9.1.min.js');
        $view->getJsManager()->loadJsFile('angularjs-1.2.18/angular.min.js');
        $view->getJsManager()->loadJsFile('angularjs-1.2.18/webcam.js');
        $view->getJsManager()->loadJsFile('dist/qr-scanner.js');
        $view->getJsManager()->loadJsFile('dist/jsqrcode-combined.min.js');
        $view->getJsManager()->loadJsFile('snapshoot/webcam.js');
        $view->getJsManager()->loadJsFile('snapshoot/webcam.min.js');
        //$view->getJsManager()->loadJsFile('bootstrap-3.2.0/ui-bootstrap-tpls-0.14.2.min.js');
        //$view->getJsManager()->loadJsFile('angularjs-1.2.18/angular-route-segment.min.js');
        $view->getJsManager()->loadJsFile('angularjs-1.2.18/angular-route.min.js');
        $view->getJsManager()->loadJsFile('angularjs-1.2.18/ui-bootstrap-tpls-0.11.0.js');
        
        $view->getJsManager()->loadJsFile('jquery/jquery-migrate-1.2.1.min.js');
        $view->getJsManager()->loadJsFile('bootstrap-3.2.0/bootstrap.min.js');
        $view->getJsManager()->loadJsFile('jquery/jquery-ui-1.10.3.custom.min.js');
        $view->getJsManager()->loadJsFile('jqgrid/i18n/grid.locale-es.js');
        $view->getJsManager()->loadJsFile('jqgrid/jquery.jqGrid.src.js');        
        $view->getJsManager()->addJsVar('exitLink',  json_encode($view->url(array('module'=>false ,'controller'=>'Index','action'=>'logout'))));        
        $view->getJsManager()->loadJsFile('application/exit.js');       
        
        
        $view->getJsManager()->loadJsFile('application/factories.js');
        $view->getJsManager()->loadJsFile('application/services.js');
        $view->getJsManager()->loadJsFile('application/directives.js');       
        echo $view->customScripts;
        $view->getJsManager()->loadJs();
        ?>

    </head>

    <body>
            <a class="header-logo" href="<?php $view->getBaseUrl("/");?>">
                <?php
                //echo '<img src="' . $view->getBaseUrl('/images/logo-masfletes.gif') . '" />'
                ?>
            </a>
            <div id="menu">
                <?php $view->MenuHelper()->displayResponsive(); ?>
            </div><!-- end of #menu -->            
     
      
        <div style="background:#eee url(<?php echo $view->getBaseUrl('/images/noisebg.png');?>); padding-bottom: 20px; padding-top: 20px;" >            
            <?php echo $layoutdata; ?>
        </div>
        <div id="footer">
            <p align="center" valign="middle">&copy; Mas Fletes.com, Servicio de Fletes econ&oacute;micos en San Luis Potos&iacute;, M&eacute;xico<br/>
                
            </p>
        </div>
<?php
    
?>
    </body>
</html>
