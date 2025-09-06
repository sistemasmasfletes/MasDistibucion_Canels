<div class="contiene-bread">

    <ol class="breadcrumb">

        <li class="active">Clientes</li>

        <li class="active "> <?php

            echo '<a href="'.$view->linkTo("/User/Store").'" >Tiendas</a>';

            ?></li>

        <li class="active"><?php 

        echo '<a href="'.$view->url(array('module'=>'User','controller'=>'Store','action'=>'category','id'=>$view->category->getId())).'">'.$view->category->getName().'</a> ';

        ?></li>

        <li class="active"><?php 

        echo '<a href="'.$view->url(array('module'=>'User','controller'=>'Store','action'=>'viewCatalog','id'=>$view->catalog->getId())).'">';

        echo $view->user->getCommercialName() ? $view->user->getCommercialName() : $view->user->getFirstName() .' '.$view->user->getLastName();

        echo ' - ' .$view->catalog->getTitle().'</a>';

        ?></li>

        <li class="active actualpg "><?php 

        echo $view->product->getName();

        ?></li>

    </ol>

</div>

<div class="container">

    <div class="row" >

        <!--<div class="span12 blockGray">-->

            <div class="blockInner">
            <?php 
            	echo '<a class="btn-default sin-padding " href="'.$view->url(array('module'=>'User','controller'=>'Store','action'=>'viewCatalog','id'=>$view->catalog->getId())).'" title="Regresar a Catalogo">';
            	echo '<span style="float:right;background-color:#08A4DA;width:30%;border-radius:5%;text-align:center;color:#fff;padding:.5em; box-shadow:-19px 12px 17px -7px rgba(13,12,12,0.58);"> << CATALOGO: '.$view->catalog->getTitle().'</span></a>';
            ?>
                <div id="messages"></div>

                <?php

                echo '<div class="sf-catalog-title"><h1>' . $view->product->getName() . '</h1></div>';

                echo '<div id="productsStoreData" >';



                echo $view->helper('Display')->showProduct($view->product);



                echo '</div>';

                ?>

            </div>

        <!--</div>-->

    </div>

</div>