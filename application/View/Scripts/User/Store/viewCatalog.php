<?php 
    $_SESSION["idcomercio"] = $view->user->getId();
?>
<div class="contiene-bread" id="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Clientes</li>
        <li class="active "> <?php
            echo '<a href="' . $view->linkTo("/User/Store") . '" >Tiendas</a>';
            ?></li>
        <li class="active"><?php
            echo '<a href="' . $view->url(array('module' => 'User', 'controller' => 'Store', 'action' => 'category', 'id' => $view->category->getId())) . '">' . $view->category->getName() . '</a> ';
            ?></li>
        <li class="active actualpg "><?php echo $view->user->getCommercialName() ? $view->user->getCommercialName() : $view->user->getFirstName() . ' ' . $view->user->getLastName(); echo ' - ' . $view->catalog->getTitle(); ?></li>
    </ol>
</div>
<div class="container">
	<?php 
	echo '<a class="btn-default sin-padding " href="'.$view->url(array('module'=>'User','controller'=>'Store','action'=>'user','id'=>$view->user->getId())).'" title="Volver a tienda">';
    echo '<span style="float:right;background-color:#08A4DA;width:30%;border-radius:5%;text-align:center;color:#fff;padding:.5em; box-shadow:-19px 12px 17px -7px rgba(13,12,12,0.58);">
          << TIENDA: '.$view->user->getCommercialName().'</span></a><br /<br /<br />';
    ?>
            
    <div class="row" >
        <div class="blockInner">                
            <?php
            echo '<div class="sf-catalog-title"><h1>' . $view->catalog->getTitle() . '</h1></div>';
            if ($view->catalog->getUrlCatalog() != "") {
                echo '<a target="_blank" href="http://' . $view->catalog->getUrlCatalog() . '"> Ver catalogo en linea </a>';
            }
            echo '<div id="productsStoreData" >';
            if (isset($_POST['action']) && !empty($_POST['action']) && isset($_POST['arguments']) && !empty($_POST['arguments'])) {
                $action = $_POST['action'];
                $argument = $_POST['arguments'];
                switch ($action) {
                    case 'test' :
                        $cadenaHTML = $view->helper('Element_ElementCatalog')->build($view->catalog, $argument);
                        echo $cadenaHTML;
                        break;
                }
            } else {
                echo $view->helper('Element_ElementCatalog')->build($view->catalog);
            }

            echo '</div>';
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    function goToPage(numPag) {
        $.ajax({url: '<?php echo $view->getBaseUrl(); ?>/User/Store/viewCatalog/id/<?php echo $view->catalog->getId(); ?>/',
            data: {action: 'test', arguments: numPag},
            type: 'post',
            success: function (output) {
                var nodo = output;

                var pos = nodo.search('<table');
                var sc = nodo.substring(pos, nodo.lenght);
                document.getElementById("productsStoreData").innerHTML = sc;
            }
        });
    }

</script>

