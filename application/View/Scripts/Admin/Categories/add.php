<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Catálogos</li>
    <li class="active "><?php echo '<a href="' . $view->url(array('action' => 'index')) . '" >Categorías</a>'?></li>
    <li class="active actualpg ">Agregar Categoría</li>
</ol>
</div>  
 
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h1>Agregar de Categor&iacute;a</h1>
                <?php
//                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->result == true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Registro insertado con exito';
                    echo '</div>';
                }
//                $view->sc->generateForm();?>
                <form method="post"   enctype="multipart/form-data">
                    <div class="col-md-6">
                    <label id="label_name" for="name">Nombre: </label>
                    <input type="text" class="" id="name" name="name" required="required"/>
                    <input type="hidden" class="" id="imagePath" name="imagePath"  >
                    </div>
                    <div class="col-md-3">
                        <input type="file" name="fileToUpload" id="filechooser" accept="image/png" onchange="uploadFile(this.value)">
                    </div>
                    <br>
                    <button id="submit" type="submit" class="btn btn-default sin-padding pers-btn icono-enviar tam-normal" />
                </form>
            </div>
        <!--</div>-->
    </div>
</div>
