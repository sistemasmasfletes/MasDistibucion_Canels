<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="shortcut icon" href="<?php echo $view->getBaseUrlPublic(); ?>/images/ui/favicon.png" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-9" />
        <title>Mas distribuci&oacute;n</title>
        
        <!--        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />-->
        <?php $view->getCssManager()->loadCssFile('ui/jquery-ui-1.8.14.custom.css'); ?>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
        <?php
        $view->getJsManager()->loadJsFile('application/exit.js');
        $view->getJsManager()->loadJsFile('bootstrap/bootstrap.js');
        $view->getCssManager()->loadCssFile('bootstrap/bootstrap.css');
        $view->getCssManager()->loadCssFile('layout/Admin/pageStyles.css');
        $view->getCssManager()->loadCss();
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

        <div id="page-header">
            <div class="container">
                <div class="row">
                    <div class="span2">
                        logo
                    </div>
                    <div class="span10">
                        <div id="helloDiv">
                            Hola alguien
                        </div>
                        <?php $view->MenuHelper()->display(); ?>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div id="page-content-extend">
            <div class="container">
              <div id="page-content">
                  <div class="row-fluid">
                    <div id="page-content-sidebar" class="span3">
                       sidebar
                    </div>
                    <div class="span9">
                        <div id="page-content-body" >
                            <?php echo $layoutdata; ?>
                        </div>
                    </div>
                  </div>
              </div>
            </div>
        </div>
        <br/>
        <div id="page-footer">
            <p align="center" valign="middle">&copy; Mas Fletes.com, Servicio de Fletes econ&oacute;micos en San Luis Potos&iacute;, M&eacute;xico<br/>
                <a href="https://www.anagramhosting.com/" target="_top" class="TextoCreditos">Dise&ntilde;o web</a> y<a href="https://www.anagramhosting.com/" target="_top" class="TextoCreditos"> Marketing web</a>: <a href="https://www.anagramhosting.com/" target="_top" class="TextoCreditos">Anagram Media Graphics</a>
            </p>
        </div>
    </body>
</html>
