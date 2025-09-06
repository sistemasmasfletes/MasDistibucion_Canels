<div class="contiene-bread">

    <ol class="breadcrumb">

    <li class="active">Panel</li>

    <li class="active actualpg ">Catálogos</li>

</ol>

</div>

<div class="container">

    <div class="row" >

        <!--<div class="span12 blockGray">-->

            <div class="blockInner">
            <h1>Mis catálogos</h1>
                <div class="contentsearch">
                    <form method="post" style="margin-right:1rem;">
                    <label>Condiciones a tus clientes</label>
                    <textarea name="msgtoclients" rows="" cols=""><?php echo $view->msgtoclient ?></textarea><br />&nbsp;&nbsp;este mensaje sera visto por tus clientes al ingresar a tu cat&aacute;logo<br>
                    <input type="submit" class="allbuttons" style="background-color:green; border: none;" value="Guardar Mensaje">
                    </form>

                    <form method="post">
                    <label>Mensaje a tus clientes</label>
                    <textarea name="msgtoclients2" rows="" cols=""><?php echo $view->msgtoclient2 ?></textarea><br /s>&nbsp;&nbsp;este mensaje sera visto por tus clientes como condiciones para adquirir tus productos<br>
                    <input type="submit" class="allbuttons" style="background-color:green; border: none;" value="Guardar Comentario">
                    </form>
					
                    <form method="post">
                    <label>Captura una direcci&oacute;n url para ser vista por tus clientes</label>
                    <textarea name="msgtoclients3" rows="" cols=""><?php echo $view->msgtoclient3 ?></textarea><br /s>&nbsp;&nbsp;Este link servir&aacute; para dirigir a tus clientes a un enlace de tu preferencia<br>
                    <input type="submit" class="allbuttons" style="background-color:green; border: none;" value="Guardar Enlace">
                    </form>
					
                </div>
                <br />
                <?php

                echo '<a class="btn btn-default sin-padding" href="' . $view->url(array('action' => 'add')) . '" ><span class="pers-btn icono-agregar-catalogo tam-normal"></span></a>';

//                echo '<a class="btn btn-default nuevo" href="' . $view->url(array('action' => 'add')) . '" ><span class="pers-btn icono-nuevo tam-normal"></span></a>';

                echo '<div class="clear" style="height:15px;" ></div>';

                $view->sc->generatetable($view->catalogs);

                ?>

            </div>

        <!--</div>-->

    </div>

</div>



