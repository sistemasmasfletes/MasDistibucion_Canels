<?php /* @var $view Model3_View */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es-ES">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Fletes en San Luis Potosí, México / Servicio de Fletes Económicos :: MasFletes.com</title>
        <?php
        $view->getCssManager()->loadCssFile('blueprint/screen.css', 'screen, projection');
        $view->getCssManager()->loadCssFile('blueprint/print.css', 'print');
        $view->getCssManager()->loadCssFile('blueprint/ie.css', 'screen', false, 'lt IE 8');
        
        $view->getCssManager()->loadCssFile('bussinesscenter/bussinesscenter.css');
        $view->getCssManager()->loadCssFile('bootstrap/bootstrap.css');
        $view->getJsManager()->loadJsFile('bootstrap/bootstrap.js');
        
        ?>
<!--        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />-->
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="span-24 last">
                <h1>BussinessCenter</h1>
            </div>
            <div class="span-24 last">
                <?php $view->LoginHelper()->login(); ?>
            </div>
            <div class="span-24 last">
                <?php $view->MenuHelper()->display(); ?>
            </div>
            
            <hr/>
            <?php
            echo $layoutdata;
            ?>
        </div>
        <?php
            //$view->getJsManager()->loadJsFile('jquery/jquery.min.js');
            if ($view->getJsManager()->hasJs())
            {
                $view->getJsManager()->loadJs();
            }
        ?>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.delete-link').click(function(e) {

                    e.preventDefault();
                    target = $(this).attr('href');

                    if(confirm('Estas seguro?')) {
                        window.location = target;
                    }

                });
            });
        </script>
    </body>
</html>
