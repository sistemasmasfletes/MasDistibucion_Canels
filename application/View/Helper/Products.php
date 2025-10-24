<?php
/**
 *  Este helper nos ayuda a la edicion de los productos
 *
 * @author H4845
 */
class View_Helper_Products extends Model3_View_Helper
{   
    public function printProductForm($formAction,$catalogs,$currentCatalog, $product = null, $creditosXMoneda)
    {
        $images = false;
        if($product != null && $product instanceof DefaultDb_Entities_Product)
        {
            $images = $product->getImages();
        }

        ?>
        <div class="hide messages">

        </div>
        <form method="post" id="productForm" class="productForm" action="<?php echo $formAction;?>">
        <!--<form method="post" id="productForm" class="well" action="<?php // echo $formAction;?>">-->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Campos que siempre deben valer 1 y estar ocultos
    const forceFields = ['price','priceList','priceCreditos','stock','width','height','depth','weight','size'];
    forceFields.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.value = 1;
            el.type = "hidden";
            const span = el.previousElementSibling;
            if (span && span.tagName.toLowerCase() === "span") {
                span.style.display = "none";
            }
        }
    });

    // Ocultar completamente campos y sus etiquetas
    const hideFields = ['priceList','priceCreditos','order',
        'maker','offer','provitionTime','newStartDate','sku',
        'warranty','color','newEndDate'
    ];
    hideFields.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = "none";
            const span = el.previousElementSibling;
            if (span && span.tagName.toLowerCase() === "span") {
                span.style.display = "none";
            }
        }
    });

    // Ocultar la pestaña "Embalaje" y su contenido
    const embalajeTab = document.querySelector('a[href="#embalaje"]');
    const embalajeContent = document.getElementById('embalaje');
    if (embalajeTab) embalajeTab.parentElement.style.display = 'none';
    if (embalajeContent) embalajeContent.style.display = 'none';

    // Ocultar pestaña de "Imágenes"
    const imgTab = document.querySelector('a[data-toggle="myModal"]');
    if (imgTab) imgTab.parentElement.style.display = 'none';
});
</script>

        
            <ul class="nav nav-tabs" id="myTab">
              <li class="active primero"><a href="#area1">Pasajero</a></li>
              <li><a href="#area2">Detalles</a></li>
              <li><a href="#embalaje">Embalaje</a></li>
              <li class="ultimo" style="display:none;">
                  <?php
                  $clase = 'disabled = "disabled"';
                  $dataToogle = '';
                  if($product != null )
                  {
                    $clase = '';
                    $dataToogle = 'myModal';
                  }
                      
                  ?>
                  <a class="<?php echo $clase;?>" data-toggle="<?php echo $dataToogle;?>" href="#myModal" >
                      Imagenes
                  </a>
              </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="area1">
                    <div class="row">
                        <div class="span5">
                            <input type="hidden" id="idProducto1" name="idProducto1" value="<?php echo $this->getProductValue($product,'id');?>" />
                                    <span>N° Nomina <small>*</small></span>
                                    <input type="text" required="required"
                                            title="ID de nomina"
                                            data-content="Identificador unico del pasajero"
                                            maxlength="80"
                                            name="id_payroll" id="id_payroll"
                                            value="<?php echo $this->getProductValue($product, 'idPayroll'); ?>"
                                        >
                                    <span>Nombre <small>*</small></span>
                                    <input type="text" required="required"
                                           title="Nombre del pasajero"
                                           data-content="Este sera el nombre que aparecera en el catalogo de pasajeros"
                                           name="name" id="name"
                                           value="<?php echo $this->getProductValue($product, 'name'); ?>"
                                           maxlength="80"
                                           >
                                           <span>Apellidos <small>*</small></span>
                                           <input type="text" required="required"
                                                title="Apellidos del pasajero"
                                                data-cotent="Estos seran los apellidos que aparecera en el catalogo de pasajeros"
                                                maxlength="80"
                                                name="last_name" id="last_name"
                                                value="<?php echo $this->getProductValue($product, 'LastName'); ?>"
                                            >
                                           <span>Cel</span>
                                           <input type="text"
                                                title="Celular del pasajero"
                                                maxlength="80"
                                                name="cell" id="cell"
                                                value="<?php echo $this->getProductValue($product, 'cell'); ?>"
                                                >

                                           <span>Precio <small>*</small></span>
                                           <input type="text"
                                           title="Precio"
                                           data-content="Solo puede agregar enteros o decimales, si el precio es 0 no se podra comprar"
                                           name="price" id="price"
                                           value="<?php echo $this->getProductValue($product, 'price'); ?>"
                                           maxlength="13"
                                           >

                                    <span>Precio Listado</span>
                                    <input type="text"
                                           title="Precio Listado"
                                           data-content="Este campo solo recibe numero enteros o decimales, e indica un precio mayor, si es definido aparecera tachado."
                                           name="priceList" id="priceList"
                                           value="<?php echo $this->getProductValue($product, 'priceList'); ?>"
                                           maxlength="13"
                                           >
                                    <input type="hidden" id="creditosXmoneda" name="creditosXmoneda" value="<?php echo $creditosXMoneda ?>" />
                                    <span>Conversión a créditos</span>     
                                    <input type="text"
                                           readonly="true"
                                           title="Precio en creditos"
                                           data-content="Este campo solo recibe numero enteros o decimales, e indica un monto mayor, si es definido aparecera tachado."
                                           name="priceCreditos" id="priceCreditos"
                                           value="<?php echo $this->getProductValue($product, 'priceCreditos'); ?>"
                                           maxlength="13"
                                           >
                                    
                                </div>
                                <div class="span5">
                                    <span>Existencia *</span>
                                    <input type="text" 
        <?php if ($this->getProductValue($product, 'variantsUse') == DefaultDb_Entities_Product::VARIANTS_USE): ?>
                                               disabled="disabled"
                                               class="disabled"
                                    <?php endif; ?>
                                           title="Existencia"
                                           data-content="Defina aqui el numero de unidades que tiene en existencia, solo se aceptan numero enteros"
                                           name="stock" id="stock"
                                           value="<?php echo $this->getProductValue($product, 'stock'); ?>"
                                           >

                                <div style="display:none" >
                                    <span>Destacado</span>
                                    <?php
                                    $myFeatured = $this->getProductValue($product, 'featured');
                                    ?>
                                    <select id="featured" name="featured">                            
                                        <option value="0" <?php echo $myFeatured == '0' ? 'selected="selected"' : ''; ?>>No destacar</option>
                                        <option value="1" <?php echo $myFeatured == '1' ? 'selected="selected"' : ''; ?>>Destacar</option>
                                    </select>
                                </div>
                                    <span>Orden</span>
                                    <input type="text"
                                           title="Orden"
                                           data-content="Defina el orden en el que aparecera su producto, entre menor sea aparecera primero"
                                           name="order" id="order"
                                           value="<?php echo $this->getProductValue($product, 'order'); ?>"
                                           >

                                    <?php
                                    $currentPriority = (int) $this->getProductValue($product, 'priority'); 
                                    $currentGender = (int) $this->getProductValue($product, 'gender');
                                    $currentDisability = (int) $this->getProductValue($product, 'disability');
                                    $currentStatus = (int) $this->getProductValue($product, 'status');
                                    $currentMethod = $this->getProductValue($product, 'NotificationMethod');
                                    $currentClassification = $this->getProductValue($product, 'Clasificacion1');
                                    ?>

                                    <span>Prioridad</span>
                                    <select id="priority" name="priority" title="Prioridad">
                                        <option value="0" <?php if ($currentPriority === 0) echo 'selected="selected"'; ?>>
                                            Sin prioridad
                                        </option>
                                        <option value="1" <?php if ($currentPriority === 1) echo 'selected="selected"'; ?>>
                                            Prioridad media
                                        </option>
                                        <option value="2" <?php if ($currentPriority === 2) echo 'selected="selected"'; ?>>
                                            Máxima prioridad
                                        </option>
                                    </select>

                                    <span>Género</span>
                                    <select id="gender" name="gender" title="Género">
                                        <option value="0" <?php if ($currentGender === 0) echo 'selected="selected"'; ?>>
                                            Masculino
                                        </option>
                                        <option value="1" <?php if ($currentGender === 1) echo 'selected="selected"'; ?>>
                                            Femenino
                                        </option>
                                    </select>
                                    <span>Discapacidad</span>
                                    <select id="disability" name="disability" title="Discapacidad">
                                        <option value="0" <?php if ($currentDisability === 0) echo 'selected="selected"'; ?>>
                                            Ninguna
                                        </option>
                                        <option value="1" <?php if ($currentDisability === 1) echo 'selected="selected"'; ?>>
                                            Visual
                                        </option>
                                        <option value="2" <?php if ($currentDisability === 2) echo 'selected="selected"'; ?>>
                                            Auditiva
                                        </option>
                                        <option value="3" <?php if ($currentDisability === 3) echo 'selected="selected"'; ?>>
                                            Motriz
                                        </option>
                                        <option value="4" <?php if ($currentDisability === 4) echo 'selected="selected"'; ?>>
                                            Intelectual
                                        </option>
                                        <option value="5" <?php if ($currentDisability === 5) echo 'selected="selected"'; ?>>
                                            Psicosocial
                                        </option>
                                    </select>
                                    <?php
                                        if (isset($product) && $product instanceof DefaultDb_Entities_Product) {
                                            $currentStatus = $product->getStatus();
                                        } else {
                                            // Si es nuevo o no está definido, ponemos 1 (activo por defecto)
                                            $currentStatus = 1;
                                        }
                                    ?>
                                    <span>Estatus <small>*</small></span>
                                    <select id="status" name="status" required title="status">
                                        <option value="1" <?php if ($currentStatus === 1) echo 'selected="selected"'; ?>>
                                            ✔️ Activo
                                        </option>
                                        <option value="0" <?php if ($currentStatus === 0) echo 'selected="selected"'; ?>>
                                            ❌ Inactivo
                                        </option>
                                    </select>
                                </div>
                    </div>
                    <div class="row">
                        <div class="span8">
                        <span>Descripci&oacute;n</span>
                        <textarea name="description" id="description" maxlength="600" ><?php echo $this->getProductValue($product,'description');?></textarea>
                    </div>
                        <div class="span3">
                            <div id="defaultImgage">
                                <?php
                                    $printables = 0;
                                    $firstImageId = null;
                                    if($images != false)
                                    {
                                        foreach($images as $i)
                                        {
                                            echo $this->_view->ManagerImages()->thumbImg(
                                                    $i->getPath(),
                                                    $product->getName(),
                                                    $product->getName(),
                                                    '',
                                                    '410',
                                                    '240'
                                                    );
                                            $printables++;
                                            $firstImageId = $i->getId(); 
                                            break;
                                        }
                                    }

                                    if($printables == 0)
                                    {
                                        echo $this->_view->ManagerImages()->img('images/products/default.jpg');
                                    }
                                ?>
                            </div>
                            
                            <div style="margin-top: 10px; text-align: center;">
                                <?php if($printables > 0 && $firstImageId): ?>
                                    <button class="btn btn-info btn-small" onclick="replaceImage(<?php echo $firstImageId; ?>, this)" title="Tomar nueva foto para reemplazar esta imagen">
                                        <i class="bi bi-camera"></i> Editar/Reemplazar Foto
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-success btn-small" onclick="openImageManager()" title="Tomar primera foto para este producto">
                                        <i class="bi bi-camera"></i> Agregar Foto
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    

                        
                   
                    
                </div>
                <div class="tab-pane" id="area2">
                    <div class="span5">
                        <span>Notificar por</span>
                        <select id="notification_method" name="notification_method">
                            <option value="" <?php if (empty($currentMethod)) echo 'selected="selected"'; ?>>
                                Selecciona una opción
                            </option>
                            <option value="S" <?php if ($currentMethod === 'S') echo 'selected="selected"'; ?>>
                                📱 SMS
                            </option>
                            <option value="W" <?php if ($currentMethod === 'W') echo 'selected="selected"'; ?>>
                                💬 WhatsApp
                            </option>
                            <option value="E" <?php if ($currentMethod === 'E') echo 'selected="selected"'; ?>>
                                ✉️ Correo electrónico
                            </option>
                        </select>

                        <span>Contacto a notificar</span>
                        <input type="text" id="notify_contact" name="notify_contact"
                            placeholder="Ej. +5215551234567 o supervisor@empresa.com"
                            maxlength="100"
                            value="<?php echo $this->getProductValue($product,'notifyContact');?>"
                            >
                            
                        <span>Oferta Especial</span>
                        <?php
                            $myOffer =  $this->getProductValue($product,'offer');
                        ?>
                        <select id="offer" name="offer">
                            <option value="0" <?php echo $myOffer=='0'?'selected="selected"':'';?>>No</option>
                            <option value="1" <?php echo $myOffer=='1'?'selected="selected"':'';?>>Si</option>
                        </select>


                        <span>Fabricante</span>
                        <input type="text"
                               title="Fabricante"
                               data-content="Campo opcional para indicar el fabricante del producto."
                               name="maker" id="maker"
                               value="<?php echo $this->getProductValue($product,'maker');?>"
                               >

                        <span>Tiempo de disponibilidad</span>
                        <?php
                            $myTime =  $this->getProductValue($product,'provitionTime');
                        ?>
                        <select id="provitionTime" name="provitionTime">
                            <option value="">Seleccione</option>
                            <option value="1-3 dias" <?php echo $myTime=='1-3 dias'?'selected="selected"':'';?>>1-3 días</option>
                            <option value="4-6 dias" <?php echo $myTime=='4-6 dias'?'selected="selected"':'';?>>4-6 días</option>
                            <option value="1-2 semanas" <?php echo $myTime=='1-2 semanas'?'selected="selected"':'';?>>1-2 semanas</option>
                            <option value="bajo pedido" <?php echo $myTime=='bajo pedido'?'selected="selected"':'';?>>Bajo pedido</option>
                        </select>

                        <span>Nuevo desde</span>
                        <input type="text"
                               title="Nuevo desde"
                               data-content="Este producto aparecera como nuevo desde la fecha indicada."
                               name="newStartDate" id="newStartDate"
                               <?php $date = $this->getProductValue($product,'newStartDate');
                               $date = $date != "" ? date_format($date, 'Y-m-d') : '';?>
                               value="<?php echo $date;?>"
                               >

                    </div>
                    <div class="span5">
                        <span>Clasificacion</span>
                        <select id="Clasificacion1" name="Clasificacion1"> 
                            <option value="Canels" <?php if ($currentClassification === 'Canels') echo 'selected="selected"'; ?>>
                                Canels
                            </option>
                            <option value="Ultra" <?php if ($currentClassification === 'Ultra') echo 'selected="selected"'; ?>>
                                Ultra
                            </option>
                        </select>
                        <span>Clasificacion 2</span>
                        <input type="text"
                        title="CLASIFICACION 2"
                        data-content="CLASIFICACION 2"
                        placeholder="Clasificacion opcional"
                        name="Clasificacion2" id="Clasificacion2"
                        value="<?php echo $this->getProductValue($product,'Clasificacion2');?>"
                        >
                        <span>Clasificacion 3</span>
                        <input type="text"
                        title="CLASIFICACION 2"
                        data-content="CLASIFICACION 2"
                        placeholder="Clasificacion opcional"
                        name="Clasificacion3" id="Clasificacion3"
                        value="<?php echo $this->getProductValue($product,'Clasificacion3');?>"
                        >
                        <!-- <span>Ruta</span>
                            <input type="text" 
                            readonly 
                            class="form-control"
                            >
                        -->
                        <span>Parada</span>
                            <select name="catalog" id="catalog" >
                                <option value="0">Seleccione</option>
                                <?php
                                 foreach ($catalogs as $c) {
                                     $extra = '';
                                     if ($c->getCatalogFather() != null) {
                                     $extra = '&nbsp;&nbsp;|&nbsp;';
                                    }
                                    $seleccionado = $currentCatalog != null && $currentCatalog->getId() == $c->getId() ? 'selected="selected"' : '';
                                    echo '<option value="' . $c->getId() . '" ' . $seleccionado . '>' . $extra . $c->getTitle() . '</option>';
                                 }
                                ?>
                            </select>
                        <span>Biometría</span>
                        <input type="text" id="biometric_data" name="biometric_data"
                            placeholder="Esperando lectura..." readonly>
                        <!-- <button type="button" id="capture_fingerprint">Capturar huella</button> -->
                        <span>Clave / SKU</span>
                        <input type="text"
                               title="Clave / SKU"
                               data-content="Si usted maneja una clave interna para sus productos, indiquela en este campo."
                               name="sku" id="sku"
                               value="<?php echo $this->getProductValue($product,'sku');?>"
                               maxlength="50"
                               >

                        <span>Garantia (meses)</span>
                        <input type="text"
                               title="Garantia"
                               data-content="En caso de manejar garantia para sus productos, indique aqui, de lo contrario deje el campo vacio"
                               name="warranty" id="warranty"
                               value="<?php echo $this->getProductValue($product,'warranty');?>"
                               maxlength="2"
                               >                        
                        <span>Color</span>
                        <input type="text"
                               title="Color"
                               data-content="En caso de que desee indicar un color para este producto, use este campo."
                               name="color" id="color"
                               value="<?php echo $this->getProductValue($product,'color');?>"
                               maxlength="30"
                               >
                        
                        <!-- <span>Habilitar Variantes</span> -->
                        <?php
                            //$variantsUse =  $this->getProductValue($product,'variantsUse');
                        ?>
                        <!--
                        <select id="variantsUse" name="variantsUse">
                            <option value="0" <?php echo $variantsUse==DefaultDb_Entities_Product::VARIANTS_NOT_USE?'selected="selected"':'';?>>No</option>
                            <option value="1" <?php echo $variantsUse==DefaultDb_Entities_Product::VARIANTS_USE?'selected="selected"':'';?>>Si</option>
                        </select>
                        -->
                        <span>Nuevo hasta</span>
                        <input type="text"
                               title="Nuevo hasta"
                               data-content="Este producto se marcara como nuevo hasta la fecha indicada."
                               name="newEndDate" id="newEndDate"
                               <?php $date = $this->getProductValue($product,'newEndDate');
                               $date = $date != "" ? date_format($date,"Y-m-d"): '';?>
                               value="<?php  echo $date; ?>"
                               >
                    </div>

                    <?php //$this->printVariants($product);?>
                    

                </div>
                <div class="tab-pane" id="embalaje">
                    <div class="span5">
                        <span>Ancho(cm)</span>
                        <input type="text"
                               title="Ancho"
                               data-content="Indique aqui lo ancho aproximado del producto"
                               name="width" id="width"
                               value="<?php echo $this->getProductValue($product,'width');?>"
                               maxlength="6"
                               >

                        <span>Alto(cm)</span>
                        <input type="text"
                               title="Alto"
                               data-content="Indique aqui lo alto aproximado del producto"
                               name="height" id="height"
                               value="<?php echo $this->getProductValue($product,'height');?>"
                               maxlength="6"
                               >

                        <span>Largo(cm)</span>
                        <input type="text"
                               title="Largo"
                               data-content="Indique aqui lo largo aproximado del producto"
                               name="depth" id="depth"
                               value="<?php echo $this->getProductValue($product,'depth');?>"
                               maxlength="6"
                               >

                        <span>Peso(kg)</span>
                        <input type="text"
                               title="Peso"
                               data-content="Indique aqui el peso aproximado del producto"
                               name="weight" id="weight"
                               value="<?php echo $this->getProductValue($product,'weight');?>"
                               maxlength="9"
                               >

                        <span>Tama&ntilde;o(cm<sup>3</sup>)</span>
                        <input type="text"
                               title="Tama&ntilde;o"
                               name="size" id="size"
                               value="<?php echo $this->getProductValue($product,'size');?>"
                               maxlength="14"
                               readonly="readonly"
                               class="disabled"
                               >
                    </div>
                </div>

            </div>
            <br>
            <div class="span12">
                <!--<div class="span2">-->
                    <button type="submit" class="btn btn-default sin-padding pers-btn icono-guardar tam-normal"></button>
                <!--</div>-->
                <div class="span2">
                    <?php echo $this->_view->ManagerImages()->img('images/loading.gif','loading','loading','id="loaderImage" class="hide"');?>
                </div>
            </div>
            <br><br>
        </form>
        <?php
            echo $this->printImageArea($this->getProductValue($product,'id'),$images);?>
        <?php
    }

private function printImageArea($idProduct,$images)
{
    $srcNoImage = $this->_view->getBaseUrlPublic().'/FixImages/noImage/1/'.$this->_view->sitio['id'];
    $opciones = $this->_view->opcionesSitios[$this->_view->sitio['categoria']];
    ?>
    <script type="text/javascript">
        var urlSelectOneImageToProduct = '<?php echo $this->_view->url(array('controller'=>'Productos','action'=>'axGetFileFromPath'));?>'
        var urlDeleteImage = '<?php echo $this->_view->url(array('controller'=>'Ajax_UserProducts','action'=>'deleteImage'));?>';
    </script>
    <div id="myModal" class="modal hide" >

          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3>Gestionar im&aacute;genes del producto</h3>
          </div>
          <div class="modal-body">
               <div id="imageUpdate" style="display: none">
                <div id="notificacionesImagenes" style="display: none;" class="error"></div>
                
                <!-- Sección para agregar nueva foto -->
                <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                    <h4>Agregar nueva foto</h4>
                    <table>
                        <tr id="sf-prod-src">
                            <td id="sf-prod-src-browser">
                                <form style="display:inline-block;" id="camera_form"
                                      class="form-horizontal"
                                      action="<?php echo $this->_view->url(array('module'=> false, 'controller'=>'Ajax_UserProducts','action'=>'uploadImage'),true);?>"
                                      method="POST" enctype="multipart/form-data">
                                    
                                    <!-- Contenedor de la cámara -->
                                    <div id="camera_container">
                                        <video id="video" width="320" height="240" autoplay style="border: 1px solid #ccc;"></video>
                                        <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                                        
                                        <div style="margin: 10px 0;">
                                            <button type="button" id="capture_btn" class="btn btn-primary">Tomar Foto</button>
                                            <button type="button" id="retake_btn" class="btn" style="display:none;">Volver a Tomar</button>
                                        </div>
                                        
                                        <!-- Vista previa de la imagen capturada -->
                                        <div id="preview_container" style="display:none;">
                                            <img id="preview" src="" alt="Vista previa" style="max-width: 320px; border: 1px solid #ccc;">
                                            <div style="margin-top: 10px;">
                                                <button type="submit" id="upload_btn" class="btn btn-success">Subir Nueva Foto</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" id="image_data" name="image_data">
                                    <input type="hidden" id="idProd" name="idProducto" value="<?php echo $idProduct;?>" />
                                    <input type="hidden" id="numImgByProd" name="numImgByProd" value="1" />
                                </form>

                                <div id="loadingImage" style="display: none;">
                                    <img alt="" src="<?php echo $this->_view->getBaseUrlPublic().'/images/loading.gif'?>"/>
                                    <span>Subiendo imagen...</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Sección de imágenes existentes -->
                <div style="padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                    <h4>Imágenes existentes</h4>
                    <div id="galeryImages">
                        <?php
                        if($images != false)
                        {
                            echo "<div class='image-gallery'>";
                            foreach($images as $index => $i)
                            {
                                echo "<div class='image-item'>";
                                echo "<div class='image-container'>";
                                echo $this->_view->ManagerImages()->thumbImg(
                                        $i->getPath(),
                                        '',
                                        '',
                                        '',
                                        '205',
                                        '120'
                                        );
                                echo "</div>";
                                echo "<div class='image-buttons'>";
                                echo "<button class=\"btn btn-danger btn-small delete-btn\" onclick=\"deleteImage(".$i->getId().", this)\">";
                                echo "<i class=\"icon-trash icon-white\"></i> Eliminar";
                                echo "</button>";
                                echo "<button class=\"btn btn-info btn-small replace-btn\" onclick=\"replaceImage(".$i->getId().", this)\">";
                                echo "<i class=\"icon-camera icon-white\"></i> Editar/Reemplazar";
                                echo "</button>";
                                echo "</div>";
                                echo "</div>";
                            }
                            echo "</div>";
                        }
                        else
                        {
                            echo "<p>No hay imágenes para este producto.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>

              <div id="notImage">
                  <h3>Para poder definir imagenes primero guarde el producto</h3>
              </div>

          </div>
          <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cerrar</a>
          </div>

        <div id="notificacionesAddProduct" style="display: none;"></div>
        
    </div>

    <!-- Modal para reemplazar imagen -->
    <div id="replaceImageModal" class="modal hide">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3>Editar/Reemplazar imagen</h3>
        </div>
        <div class="modal-body">
            <p>Tomar una nueva foto para reemplazar la imagen existente:</p>
            <form id="replace_camera_form" method="POST" enctype="multipart/form-data">
                <div id="replace_camera_container">
                    <video id="replace_video" width="320" height="240" autoplay style="border: 1px solid #ccc;"></video>
                    <canvas id="replace_canvas" width="320" height="240" style="display:none;"></canvas>
                    
                    <div style="margin: 10px 0;">
                        <button type="button" id="replace_capture_btn" class="btn btn-primary">Tomar Nueva Foto</button>
                        <button type="button" id="replace_retake_btn" class="btn" style="display:none;">Volver a Tomar</button>
                    </div>
                    
                    <div id="replace_preview_container" style="display:none;">
                        <img id="replace_preview" src="" alt="Vista previa" style="max-width: 320px; border: 1px solid #ccc;">
                        <div style="margin-top: 10px;">
                            <button type="button" id="replace_upload_btn" class="btn btn-success">Reemplazar Imagen</button>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" id="replace_image_data" name="image_data">
                <input type="hidden" id="replace_idProd" name="idProducto" value="<?php echo $idProduct;?>" />
                <input type="hidden" id="replace_image_id" name="image_id" value="" />
            </form>
            
            <div id="replace_loadingImage" style="display: none;">
                <img alt="" src="<?php echo $this->_view->getBaseUrlPublic().'/images/loading.gif'?>"/>
                <span>Reemplazando imagen...</span>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancelar</a>
        </div>
    </div>

    <input type="hidden" id="urlNoImage" value="<?php echo $srcNoImage;?> "/>
    
    <script type="text/javascript">
    var stream = null;
    var replaceStream = null;
    var currentImageIdToReplace = null;

    function replaceImage(imageId, buttonElement) {
        currentImageIdToReplace = imageId;
        $('#replace_image_id').val(imageId);
        $('#replaceImageModal').modal('show');
    }

    function openImageManager() {
        $('#myModal').modal('show');
    }

    function startCamera(videoElement) {       
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Tu navegador no soporta el acceso a la cámara. Usa Chrome, Firefox o Edge.');
            return;
        }

        navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: 320, 
                height: 240 
            } 
        })
        .then(function(mediaStream) {
            if (videoElement.id === 'video') {
                stream = mediaStream;
            } else {
                replaceStream = mediaStream;
            }
            videoElement.srcObject = mediaStream;
        })
        .catch(function(err) {
            console.error('Error al acceder a la cámara:', err);
            alert('No se pudo acceder a la cámara. Asegúrate de permitir el acceso.');
        });
    }

    function stopCamera(streamToStop) {
        if (streamToStop) {
            var tracks = streamToStop.getTracks();
            for (var i = 0; i < tracks.length; i++) {
                tracks[i].stop();
            }
        }
    }

    function capturePhoto(video, canvas, preview, previewContainer, captureBtn, retakeBtn, uploadBtn, imageDataInput) {
        var context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        var imageData = canvas.toDataURL('image/jpeg', 0.8);
        
        preview.src = imageData;
        imageDataInput.value = imageData;
        
        previewContainer.style.display = 'block';
        if (uploadBtn) uploadBtn.style.display = 'inline-block';
        retakeBtn.style.display = 'inline-block';
        captureBtn.style.display = 'none';
    }

    function deleteImage(imageId, buttonElement) {
        if (!confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
            return;
        }

        var imageItem = buttonElement.closest('.image-item');
        imageItem.style.opacity = '0.5';
        
        var xhr = new XMLHttpRequest();
        var formData = new FormData();
        formData.append('imageId', imageId);
        
        xhr.open('POST', urlDeleteImage, true);
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        imageItem.remove();
                        alert('Imagen eliminada correctamente');
                        if (document.querySelectorAll('.image-item').length === 0) {
                            location.reload();
                        }
                    } else {
                        alert('Error: ' + data.error);
                        imageItem.style.opacity = '1';
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    alert('Error al procesar la respuesta');
                    imageItem.style.opacity = '1';
                }
            } else {
                alert('Error de conexión');
                imageItem.style.opacity = '1';
            }
        };
        
        xhr.onerror = function() {
            alert('Error de conexión');
            imageItem.style.opacity = '1';
        };
        
        xhr.send(formData);
    }

    document.addEventListener('DOMContentLoaded', function() {
        var video = document.getElementById('video');
        var canvas = document.getElementById('canvas');
        var captureBtn = document.getElementById('capture_btn');
        var retakeBtn = document.getElementById('retake_btn');
        var preview = document.getElementById('preview');
        var previewContainer = document.getElementById('preview_container');
        var uploadBtn = document.getElementById('upload_btn');
        var imageDataInput = document.getElementById('image_data');
        var loadingImage = document.getElementById('loadingImage');

        var replaceVideo = document.getElementById('replace_video');
        var replaceCanvas = document.getElementById('replace_canvas');
        var replaceCaptureBtn = document.getElementById('replace_capture_btn');
        var replaceRetakeBtn = document.getElementById('replace_retake_btn');
        var replacePreview = document.getElementById('replace_preview');
        var replacePreviewContainer = document.getElementById('replace_preview_container');
        var replaceUploadBtn = document.getElementById('replace_upload_btn');
        var replaceImageDataInput = document.getElementById('replace_image_data');
        var replaceLoadingImage = document.getElementById('replace_loadingImage');

        if (captureBtn) {
            captureBtn.addEventListener('click', function() {
                capturePhoto(video, canvas, preview, previewContainer, captureBtn, retakeBtn, uploadBtn, imageDataInput);
                stopCamera(stream);
            });
        }

        if (retakeBtn) {
            retakeBtn.addEventListener('click', function() {
                previewContainer.style.display = 'none';
                if (uploadBtn) uploadBtn.style.display = 'none';
                retakeBtn.style.display = 'none';
                captureBtn.style.display = 'inline-block';
                imageDataInput.value = '';
                startCamera(video);
            });
        }

        if (replaceCaptureBtn) {
            replaceCaptureBtn.addEventListener('click', function() {
                capturePhoto(replaceVideo, replaceCanvas, replacePreview, replacePreviewContainer, replaceCaptureBtn, replaceRetakeBtn, replaceUploadBtn, replaceImageDataInput);
                stopCamera(replaceStream);
            });
        }

        if (replaceRetakeBtn) {
            replaceRetakeBtn.addEventListener('click', function() {
                replacePreviewContainer.style.display = 'none';
                if (replaceUploadBtn) replaceUploadBtn.style.display = 'none';
                replaceRetakeBtn.style.display = 'none';
                replaceCaptureBtn.style.display = 'inline-block';
                replaceImageDataInput.value = '';
                startCamera(replaceVideo);
            });
        }

        var cameraForm = document.getElementById('camera_form');
        if (cameraForm) {
            cameraForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!imageDataInput.value) {
                    alert('Por favor, toma una foto primero.');
                    return;
                }

                loadingImage.style.display = 'block';
                if (uploadBtn) uploadBtn.disabled = true;
                
                var xhr = new XMLHttpRequest();
                var formData = new FormData(this);
                
                xhr.open('POST', this.action, true);
                
                xhr.onload = function() {
                    loadingImage.style.display = 'none';
                    if (uploadBtn) uploadBtn.disabled = false;
                    
                    if (xhr.status === 200) {
                        try {
                            var data = JSON.parse(xhr.responseText);
                            if (data.errorImg) {
                                alert('Error: ' + data.errorImg);
                            } else {
                                alert('Imagen agregada correctamente');
                                $('#myModal').modal('hide');
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            }
                        } catch (e) {
                            alert('Error al procesar la respuesta del servidor');
                        }
                    } else {
                        alert('Error en la conexión con el servidor');
                    }
                };
                
                xhr.onerror = function() {
                    loadingImage.style.display = 'none';
                    if (uploadBtn) uploadBtn.disabled = false;
                    alert('Error de conexión');
                };
                
                xhr.send(formData);
            });
        }

        if (replaceUploadBtn) {
            replaceUploadBtn.addEventListener('click', function() {
                if (!replaceImageDataInput.value) {
                    alert('Por favor, toma una foto primero.');
                    return;
                }

                replaceLoadingImage.style.display = 'block';
                replaceUploadBtn.disabled = true;
                
                var xhr = new XMLHttpRequest();
                var formData = new FormData();
                formData.append('image_data', replaceImageDataInput.value);
                formData.append('idProducto', document.getElementById('replace_idProd').value);
                formData.append('image_id', currentImageIdToReplace);
                formData.append('replace_existing', 'true');
                
                xhr.open('POST', '<?php echo $this->_view->url(array('module'=> false, 'controller'=>'Ajax_UserProducts','action'=>'uploadImage'),true);?>', true);
                
                xhr.onload = function() {
                    replaceLoadingImage.style.display = 'none';
                    replaceUploadBtn.disabled = false;
                    
                    if (xhr.status === 200) {
                        try {
                            var data = JSON.parse(xhr.responseText);
                            if (data.errorImg) {
                                alert('Error: ' + data.errorImg);
                            } else {
                                alert('Imagen reemplazada correctamente');
                                $('#replaceImageModal').modal('hide');
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            }
                        } catch (e) {
                            alert('Error al procesar la respuesta del servidor');
                        }
                    } else {
                        alert('Error en la conexión con el servidor');
                    }
                };
                
                xhr.onerror = function() {
                    replaceLoadingImage.style.display = 'none';
                    replaceUploadBtn.disabled = false;
                    alert('Error de conexión');
                };
                
                xhr.send(formData);
            });
        }

        // Eventos de modal principal
        $('#myModal').on('shown.bs.modal', function() {
            if (video) startCamera(video);
        });

        $('#myModal').on('hidden.bs.modal', function() {
            stopCamera(stream);
        });

        // Eventos de modal de reemplazo
        $('#replaceImageModal').on('shown.bs.modal', function() {
            if (replaceVideo) startCamera(replaceVideo);
        });

        $('#replaceImageModal').on('hidden.bs.modal', function() {
            stopCamera(replaceStream);
            // Reset del formulario de reemplazo
            if (replacePreviewContainer) replacePreviewContainer.style.display = 'none';
            if (replaceUploadBtn) replaceUploadBtn.style.display = 'none';
            if (replaceRetakeBtn) replaceRetakeBtn.style.display = 'none';
            if (replaceCaptureBtn) replaceCaptureBtn.style.display = 'inline-block';
            if (replaceImageDataInput) replaceImageDataInput.value = '';
        });
    });
    </script>

    <style>
    .image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    .image-item {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        text-align: center;
        width: 250px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .image-container {
        margin-bottom: 10px;
    }
    .image-buttons {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .btn-small {
        padding: 6px 12px;
        font-size: 12px;
        width: 100%;
    }
    .delete-btn {
        background-color: #d9534f;
        border-color: #d43f3a;
    }
    .replace-btn {
        background-color: #5bc0de;
        border-color: #46b8da;
    }
    .image-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transition: box-shadow 0.3s ease;
    }
    </style>
     <?php
    $this->_view->getJsManager()->loadJsFile('uploader_jquery/jquery.fileupload.js');
    $this->_view->getJsManager()->loadJsFile('uploader_jquery/jquery.fileupload-ui.js');
    $this->_view->getJsManager()->loadJsFile('uploader_jquery/application.js');
}

    private function getProductValue($product,$fielName)
    {
        $value = '';

        if($product instanceof DefaultDb_Entities_Product )
        {
            try
            {
                $aux = 'get'.ucfirst($fielName);
                $value = $product->$aux();
            }
            catch(Exception $exc)
            {
            }
        }

        return $value;
    }

    public function printVariants($product)
    {
        ?>
        <span class="span3">Variantes</span>
        <div id="sheepItForm">
        <div id="sheepItForm_controls">
            <div id="sheepItForm_add"><a><span>Agregar Variante</span></a></div>
        </div>
        <?php $cont=1; ?>
        <?php if($product): ?>
        <?php foreach ($product->getVariants() as $variant):?>
        <div id="pregenerated_form_<?php echo $cont++;?>" class="pregenerated span10">
            <input type="hidden"
               name="ids[]" id="sheepItForm_#index#_ids"
               value="<?php echo $variant->getId();?>"
               >
            <input
               id="sheepItForm_#index#_description"  required="required"
               type="text" class="span6"
               title="Nombre de Variante"
               placeholder="Escriba descripcion de la variante…"
               data-content="Indique la descripción de la variante."
               name="descriptionVariant[]"
               value="<?php echo $variant->getDescription();?>"
               >
            <input
               id="sheepItForm_#index#_stock"  required="required"
               type="text" class="span2"
               title="Existencias de Variante"
               placeholder="Existencias…"
               data-content="Indique las existencias de la variante."
               name="stockVariant[]" id="stockVariant_<?php echo $variant->getId(); ?>"
               value="<?php echo $variant->getStock();?>"
               >
            <div class="span2">
            <a id="sheepItForm_remove_current">Eliminar</a>
            </div>
        </div>
        <?php endforeach;?>
        <?php endif;?>

        <div id="sheepItForm_template" class="span10">
            <input type="hidden"
               name="ids[]" id="sheepItForm_#index#_ids"
               value="0"
               >
            <input id="sheepItForm_#index#_description"  required="required"
               type="text" class="span6"
               title="Nombre de Variante"
               placeholder="Escriba descripcion de la variante…"
               data-content="Indique la descripción de la variante."
               name="descriptionVariant[]"
               >
            <input id="sheepItForm_#index#_stock"  required="required"
               type="text" class="span2"
               title="Existencias de Variante"
               placeholder="Existencias…"
               data-content="Indique las existencias de la variante."
               name="stockVariant[]"
               >
            <div class="span2">
            <a id="sheepItForm_remove_current">Eliminar</a>
            </div>
        </div>
        <div id="sheepItForm_noforms_template" class="span10">No existen variantes</div>
        </div>
        <?php
    }
}