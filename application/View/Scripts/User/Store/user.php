<div class="contiene-bread">

    <ol class="breadcrumb">

        <li class="active">Clientes</li>

        <li class="active "> <?php

            echo '<a href="'.$view->linkTo("/User/Store").'" >Tiendas</a>';

            ?></li>

        <li class="active"><?php 

        echo '<a href="'.$view->url(array('module'=>'User','controller'=>'Store','action'=>'category','id'=>$view->category->getId())).'">'.$view->category->getName().'</a> ';

        ?></li>

        <li class="active actualpg "><?php echo $view->user->getCommercialName();?></li>

    </ol>

</div>

<div class="container">

    <div class="row" >

        <!--<div class="span12 blockGray">-->

            <div class="blockInner">

                



                <?php

                $mostrarname = ($view->user->getCommercialName() != "")?$view->user->getCommercialName():$view->user->getFirstName()." ".$view->user->getLastName() ;
                echo '<h3>'.$mostrarname.'</h3>';
                
                

                if(Model3_Auth::getCredentials('type') == DefaultDb_Entities_User::USER_CLIENT) 

                {

                    echo '<div id="addFavoriteBuyer" style="display:'.($view->favoriteFlagBuyer ? 'none' : 'block').'">';

                    echo '<a id="btn_add_buyer_'.$view->user->getId().'" onclick="addToFavoriteBuyer(\'btn_add_buyer_'.$view->user->getId().'\')" href="#" class="" title="Agregar a Clientes Favoritos">';

                    echo '<input id="typeFavoriteBuyer" type="hidden" value="'.DefaultDb_Entities_FavoriteUsers::FAVORITE_BUYER.'"/>';

                    echo '<img alt="Agregar a Favoritos" src="'.$view->getBaseUrlPublic().'/images/iconos/agregar-cliente-favorito.png"/>';

                    echo '</a>';

                    echo '</div>';

                    

                    echo '<div id="addFavoriteSeller" style="display:'.($view->favoriteFlagSeller ? 'none' : 'block').'">';

                    echo '<a id="btn_add_seller_'.$view->user->getId().'" href="#" onclick="addToFavoriteSeller(\'btn_add_seller_'.$view->user->getId().'\')" class="" title="Agregar a Proveedores Favoritos">';

                    echo '<input id="typeFavoriteSeller" type="hidden" value="'.DefaultDb_Entities_FavoriteUsers::FAVORITE_SELLER.'"/>';

                    echo '<img alt="Agregar a Favoritos" src="'.$view->getBaseUrlPublic().'/images/iconos/agregar-proveedor-favorito.png"/>';

                    echo '</a>';

                    echo '</div>';

                }

                echo '<div id="removeFavoriteBuyer" style="display:'.($view->favoriteFlagBuyer ? 'block' : 'none').'">';

                echo '<a id="btn_remove_'.$view->user->getId().'" href="#" class="" onclick="removeFromFavoriteBuyer(\'btn_remove_'.$view->user->getId().'\')" title="Remover de Clientes Favoritos">';

                echo '<img alt="Remover de Favoritos" src="'.$view->getBaseUrlPublic().'/images/iconos/quitar-cliente-favorito.png"/>';

                echo '</a>';

                echo '</div>';

                echo '<ul>';

                

                echo '<div id="removeFavoriteSeller" style="display:'.($view->favoriteFlagSeller ? 'block' : 'none').'">';

                echo '<a id="btn_remove_'.$view->user->getId().'" href="#" class="" onclick="removeFromFavoriteSeller(\'btn_remove_'.$view->user->getId().'\')" title="Remover de Proveedores Favoritos">';

                echo '<img alt="Remover de Favoritos" src="'.$view->getBaseUrlPublic().'/images/iconos/quitar-proveedor-favorito.png"/>';

                echo '</a>';

                echo '</div>';

                ?>

                <br>

                <p><h4>Cat&aacute;logos</h4></p>

                <?php

                echo '<ul>';

                foreach ($view->user->getCatalogs() as $cat)

                {

                    echo '<li>';

                    echo '<a href="'.$view->url(array('controller' => 'Store', 'action' => 'viewCatalog', 'id' => $cat->getId())).'">'.$cat->getTitle().'</a>';

                    echo '</li>';

                }

                echo '</ul>';

                ?>

            </div>
            <?php 
		        echo '<a class="btn-default sin-padding " href="'.$view->url(array('module'=>'User','controller'=>'Store','action'=>'viewCart')).'" title="Ver Carrito">';
	    	    echo '<span style="float:right;background-color:#08A4DA;width:20%;border-radius:5%;text-align:center;color:#fff;padding:.5em; box-shadow:-19px 12px 17px -7px rgba(13,12,12,0.58);">Ir a Carrito</span></a>';
            ?>
            
        <!--</div>-->

    </div>
    <div style="padding:0.5em;">
    <span> Tel. <?php echo $view->user->getPhone(); ?> </span><br />
    <span> Email. <?php echo $view->user->getMail(); ?> </span>
    </div>
    
</div>



