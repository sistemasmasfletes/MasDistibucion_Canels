<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Panel</li>
    <li class="active "> <?php echo '<a href="' . $view->url(array('action' => 'index')) . '" >Catálogos</a>';?></li>
    <li class="active actualpg ">Editar catálogo</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Edición de mis catalogos</h1>
                <?php
//                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->result == true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Registro modificado con exito';
                    echo '</div>';
                }
                if($view->catalog instanceof DefaultDb_Entities_Catalog)
                {
                ?>
                <form method="post">
                    <label for="title">Titulo: </label>
                    <input type="text" class="" value="<?php echo $view->catalog->getTitle(); ?>" id="title" name="title" required="required"/>
                    <br>
                    <label for="catalogFather">Catalogo padre: </label>
                    <select class="" id="catalogFather" name="catalogFather">
                        <option value="0">Seleccione...</option>
                        <?php
                            foreach ($view->catalogs as $c)
                            {
                                if($c->getCatalogFather() == null && $c->getId() != $view->catalogId)
                                {
                                    $seleccionado = $view->catalog->getCatalogFather() != null && $view->catalog->getCatalogFather()->getId() == $c->getId() ? 'selected="selected"' : '' ;
                                    echo '<option value="'.$c->getId().'" '.$seleccionado.'>'.$c->getTitle().'</option>';
                                }
                            }
                        ?>
                    </select>
                    <br>
                    <label for="title">Url del Cat&aacute;logo: </label>
                    <?php $urlCatalog = $view->catalog->getUrlCatalog() != "" ? $view->catalog->getUrlCatalog() :'';?>
                    <input type="text" class="" value="<?php echo $urlCatalog ?>" id="urlCatalog" name="urlCatalog" />
                    <br>
                    <input type="submit" class=" btn-default sin-padding pers-btn icono-enviar tam-normal" style="color: transparent !important;">
                </form>
                <?php
                }
                ?>
            </div>
        <!--</div>-->
    </div>
</div>