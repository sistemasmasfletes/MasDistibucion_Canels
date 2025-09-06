<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Mis Datos</li>
        <li class="active actualpg ">Password</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <?php
                if ($view->resp === true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'La contrase&ntilde;a ha sido modificada';
                    echo '</div>';
                }
                else if($view->error != false)
                {
                    echo '<div class="alert alert-error">';
                    echo $view->error;
                    echo '</div>';
                }
                ?>
                <h1>Cambio de contrase&ntilde;a</h1>
                <div class="clear ui-widget ui-widget-content ui-corner-bottom">
                    <br/>
                    <form method="post" action="password">
                        <table>
                            <tr>
                                <th>Contrase&ntilde;a Anterior:</th>
                                <td><input type="password" id="passOld" name="passOld" value=""/></td>
                            </tr>
                            <tr>
                                <th>Nueva Contrase&ntilde;a:</th>
                                <td><input type="password" id="passNew" name="passNew" value=""/></td>
                            </tr>
                            <tr>
                                <th>Confirme Contrase&ntilde;a:</th>
                                <td><input type="password" id="passRepeat" name="passRepeat" value=""/></td>
                            </tr>
                        </table>
                        <input type="submit" class="btn btn-default sin-padding pers-btn icono-guardar tam-normal" value=""/>
                    </form>
                </div>
            </div>
        <!--</div>-->
    </div>
</div>