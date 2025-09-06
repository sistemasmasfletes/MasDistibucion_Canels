<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Panel</li>
    <li class="active "> <?php echo '<a href="' . $view->url(array('action' => 'index')) . '" >Catálogos</a>';?></li>
    <li class="active actualpg ">Agregar Catálogo</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Alta de Catalogos</h1>
                <?php
//                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->result == true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Registro insertado con exito';
                    echo '</div>';
                }
                //$view->sc->generateForm();
                ?>
                <form method="post">
                    <label for="title">Titulo: </label>
                    <input type="text" class="" id="title" name="title" required="required"/>
                    <br>
                    <label for="catalogFather">Catalogo padre: </label>
                    <select class="" id="catalogFather" name="catalogFather">
                        <option value="0">Seleccione...</option>
                        <?php
                            foreach ($view->catalogs as $c)
                            {
                                if($c->getCatalogFather() == null)
                                {
                                    echo '<option value="'.$c->getId().'">'.$c->getTitle().'</option>';
                                }
                                
                            }
                        ?>
                    </select>
                    <br>
                    <label for="title">Url del Cat&aacute;logo: </label>
                    <input type="text" class="" id="urlCatalog" name="urlCatalog" />
                    <br>
                    <button type="submit" class="btn btn-default sin-padding pers-btn icono-enviar tam-normal"/>
                </form>
            </div>
        <!--</div>-->
    </div>
</div>
