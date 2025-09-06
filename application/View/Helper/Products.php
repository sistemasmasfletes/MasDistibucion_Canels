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

        
            <ul class="nav nav-tabs" id="myTab">
              <li class="active primero"><a href="#area1">Producto</a></li>
              <li><a href="#area2">Detalles</a></li>
              <li><a href="#embalaje">Embalaje</a></li>
              <li class="ultimo">
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
                                    <span>Nombre <small>*</small></span>
                                    <input type="text" required="required"
                                           title="Nombre del producto"
                                           data-content="Este sera el titulo del producto que aparecera en la tienda"
                                           name="name" id="name"
                                           value="<?php echo $this->getProductValue($product, 'name'); ?>"
                                           maxlength="80"
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
                                    <span>Catalogo</span>
                                    <select name="catalog" id="catalog" disabled="none">
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


                                    <span>Destacado</span>
        <?php
        $myFeatured = $this->getProductValue($product, 'featured');
        ?>
                                    <select id="featured" name="featured">                            
                                        <option value="0" <?php echo $myFeatured == '0' ? 'selected="selected"' : ''; ?>>No destacar</option>
                                        <option value="1" <?php echo $myFeatured == '1' ? 'selected="selected"' : ''; ?>>Destacar</option>
                                    </select>

                                    <span>Orden</span>
                                    <input type="text"
                                           title="Orden"
                                           data-content="Defina el orden en el que aparecera su producto, entre menor sea aparecera primero"
                                           name="order" id="order"
                                           value="<?php echo $this->getProductValue($product, 'order'); ?>"
                                           >
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
                                if($images != false)
                                {
                                    foreach($images  as $i)
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
                                        break;
                                    }
                                }

                                if($printables == 0)
                                {
                                    echo $this->_view->ManagerImages()->img('images/products/default.jpg');
                                }
                                
                            ?>
                        </div>
                        
                    </div>
                    </div>
                    

                        
                   
                    
                </div>
                <div class="tab-pane" id="area2">
                    <div class="span5">
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
        </script>
        <div id="myModal" class="modal hide" >

              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Agregar im&aacute;genes al producto</h3>
              </div>
              <div class="modal-body">
                   <div id="imageUpdate" style="display: none">
                    <div id="notificacionesImagenes"  style="display: none;" class="error"></div>
                    <table>
                        <tr id="sf-prod-src">
                            <td id="sf-prod-src-browser">
                                <span style="float:left;margin:5px 5px 0 0;">Im&aacute;genes:</span><br>
                                    <form style="display:inline-block;" id="file_upload"
                                          class="form-horizontal"
                                          action="<?php echo $this->_view->url(array('module'=> false, 'controller'=>'Ajax_UserProducts','action'=>'uploadImage'),true);?>"
                                          method="POST" enctype="multipart/form-data">
                                        <input type="file" name="archivos[]" id="archivos" multiple>
                                        <!--<button class="btn" type="submit">Upload</button>-->

                                        <input type="hidden" id="idProd" name="idProducto" value="<?php echo $idProduct;?>" />
                                        <input type="hidden" id="numImgByProd" name="numImgByProd" value="1" />
                                    </form>

                                    <table id="files"></table>
                                    <div id="loadingImage" style="display: none;">
                                        <img alt="" src="<?php echo $this->_view->getBaseUrlPublic().'/images/loading.gif'?>"/>
                                    </div>
                            </td>
                        </tr>
                        <tr>
                             <td colspan="2">
                                 <hr/>
                                 <!--<h3>Im&aacute;genes actualmente asociadas al producto : </h3>-->
                                 <div id="galeryImages">
                                     <?php
                                        if($images != false)
                                        {
                                            echo "<table>";
                                            foreach($images  as $i)
                                            {
                                                echo "<tr>";
                                                echo "<td>";
                                                echo $this->_view->ManagerImages()->thumbImg(
                                                        $i->getPath(),
                                                        '',
                                                        '',
                                                        '',
                                                        '205',
                                                        '120'
                                                        );
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<button class=\"ui-state-default ui-corner-all\" title=\"Eliminar\" onclick=\"deleteImage(".$i->getId().")\">";
                                                echo "<span class=\"ui-icon ui-icon-trash\">Eliminar<\/span>";
                                                echo "<\/button>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                            echo "</table>";
                                        }
                                     ?>
                                 </div>
                                 <div></div>
                             </td>
                        </tr>
                    </table>
                </div>

                  <div id="notImage">
                      <h3>Para poder definir imagenes primero guarde el producto</h3>
                  </div>

              </div>
              <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Close</a>
              </div>



            <div id="notificacionesAddProduct" style="display: none;"></div>
            
        </div>
        <input type="hidden" id="urlNoImage" value="<?php echo $srcNoImage;?> "/>
         <?php
        //La documentacion para la carga de imagenes en 1 click menciona que estos archivos
        //deben ser cargados en cualquier parte despues del form del selector
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