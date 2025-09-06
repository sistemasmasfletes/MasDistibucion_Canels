<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Negocios</li>
        <li class="active actualpg ">Tiendas</li>
    </ol>
</div>
<div class="container">
    <div class="row">

    </div>
    <div class="row" >
        <span></span>
        <form id="formStoreFilter" method="post">
			<div class="divhorizontal divhorizontalcontent" style="width:100%;">
				<div class="divhorizontal">
					<label>Pa&iacute;s:</label>
					<select name="country" id="country" onchange="getStates(this.value)">
	                	<?php
	                    foreach ($view->countries as $pais) {
	                    	$seleccion = $view->country != NULL ? ((int) $view->country == $pais->getId() ? 'selected = "selected"' : '') : ((int)$pais->getId() == 143 ? 'selected = "selected"' : '');
	                        echo '<option value="' . $pais->getId() . '" ' . $seleccion . '>' . $pais->getNombre() . '</option>';
	                  	}
	                    ?>
	           		</select>
           		</div>
           		<div class="divhorizontal">
					<label>Estado:</label>
					<select name="state" id="state" onchange="getCities(this.value)">
                    </select>
                    <input name="hiddenstate" type="hidden" id="hiddenstate" value="<?php echo $view->state; ?>" />
           		</div>
           		<div class="divhorizontal">
					<label>Ciudad:</label>
           			<select name="city" id="city">
                    </select>
                    <input name="hiddencity" type="hidden" id="hiddencity" value="<?php echo $view->city; ?>" />
           		</div>
			</div>
			<div class="divhorizontal" style=" width:100%;">
				<div class="divhorizontal" style="width:70%">
					<label style="width: 30%;">Tienda &oacute; Direcci&oacute;n:</label>
                    <input name="usernameTxt" required class="order search-query" style="width:70%;" id="usernameTxt" autocomplete="off" type="text" class="input300" value="<?php  echo $view->usernameTxt; ?>" placeholder="Buscar"/><span></span>
                    <input name="hiddenBranch" type="hidden" id="hiddenBranch" value="<?php  echo $view->hiddenBranch; ?>" />
				</div>
				<div class="suggestionUsers-sf" id="suggestedUsers" style="height:8em; margin-top:11.5em;">
                    <div id="suggestedUsersList"></div>
                </div>
	            <div class="divhorizontal" style="width:30%">
	                <div class="pull-right" style="padding-right:5%;width:100%;">
	                    <input type="submit" class="btn btn-default sin-padding pers-btn icono-filtrar tam-normal" value=""/>
	                </div>
	            </div>
			</div>
           	<script type="text/javascript">
            	$(document).ready(function () {
                	document.getElementById("country").onchange();
               	});
          	</script>
            <?php if ($view->check) { ?>
            	<script type="text/javascript">
                	$(document).ready(function () {
                    	$("#chkTiendasSucursal").prop("checked", true);
                   	});
              	</script>
                <?php } else { ?>
                	<script type="text/javascript">
                    	$(document).ready(function () {
                        	$("#chkTiendasSucursal").prop("checked", false);
                      	});
                 	</script>
            	<?php } ?>
        </form>
        <!--<div class="span12 blockGray">-->
        <div class="row">
            <div class="blockInner">
                <h1>Categor&iacute;as</h1>

                <?php
                echo '<div class="contenedor-categorias">';
                if($view->filterone ==  TRUE){
	                foreach ($view->categories as $cat) {
	                    /*echo '<a class="btn  square-bordered" href="' . $view->url(array('controller' => 'Store', 'action' => 'category', 'id' => $cat->getCategoryId()->getId())) . '">';
	                    echo '<div class="contenido-categoria"><img style="" src="' . $view->getBaseUrl() . '/' . $cat->getCategoryId()->getImagePath() . '" class="icono-categoria"/><label>' . $cat->getCategoryId()->getName() . '</labek></div>';*/
	                    echo '<a class="btn  square-bordered" href="' . $view->url(array('controller' => 'Store', 'action' => 'category', 'id' => $cat->getId())) . '">';
	                    echo '<div class="contenido-categoria"><img style="" src="' . $view->getBaseUrl() . '/' . $cat->getImagePath() . '" class="icono-categoria"/><label>' . $cat->getName() . '</labek></div>';
	                    echo '</a>';
	                }
				}else{
	                /*foreach ($view->categories as $cat) {
	                    echo '<a class="btn  square-bordered" href="' . $view->url(array('controller' => 'Store', 'action' => 'category', 'id' => $cat->getId())) . '">';
	                    //echo '<a href="'.$view->url(array('controller' => 'Store', 'action' => 'category', 'id' => $cat->getId())).'">'.$cat->getName().'</a>';                    
	                    echo '<div class="contenido-categoria"><img style="max-width: 35px;max-height: 35px;" src="' . $view->getBaseUrl() . '/' . $cat->getImagePath() . '" class="icono-categoria"/><label>' . $cat->getName() . '</labek></div>';
	                    echo '</a>';
	                }*/
					foreach ($view->categories as $cat) {
						if($cat->getCategory()->getId() != 171){ /*ESTO SOLO ES PARA NO MOSTRAR LA CATEGORIA DE PUNTOS DE VENTA EN PROCESO*/
							echo '<a class="btn  square-bordered" href="' . $view->url(array('controller' => 'Store', 'action' => 'category', 'id' => $cat->getCategory()->getId())) . '">';		
							//echo '<a href="'.$view->url(array('controller' => 'Store', 'action' => 'category', 'id' => $cat->getId())).'">'.$cat->getName().'</a>';		
							echo '<div class="contenido-categoria"><img style="" src="' . $view->getBaseUrl() . '/' . $cat->getCategory()->getImagePath() . '" class="icono-categoria"/><label>' . $cat->getCategory()->getName() . '</label></div>';		
							echo '</a>';						
						}
					}
                }
                echo '</div>';
                ?>
            </div>
        </div>
        <!--</div>-->
    </div>
</div>
