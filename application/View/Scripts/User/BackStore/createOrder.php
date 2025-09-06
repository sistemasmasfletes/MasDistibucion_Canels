<?php /* @var $view Model_View*/?>
<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Panel</li>
        <li class="active "> <?php
            $action = ($view->isBuyer != null) ? 'shopping' : 'orders';
            if ($action == 'shopping') {
                echo '<a href="' . $view->url(array('action' => $action)) . '" >Recolecciones</a>';
            } else {
                echo '<a href="' . $view->url(array('action' => $action)) . '" >Entregas</a>';
            }
            ?>
        </li>
        <li class="active actualpg ">Nueva Orden</li>
    </ol>
</div>

<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <?php
                if($view->result == true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Orden registrada con exito';
                    echo '</div>';
                }?>
            
                <h1>Nueva Orden</h1>
                <?php
                
                    $action = ($view->isBuyer != null) ? 'shopping': 'orders';
//                    echo '<a class="btn" href="' . $view->url(array('action' => $action)) . '" >Regresar</a>';
                    echo '<div class="clear" style="height:15px;" ></div>';
                ?>
                <?php echo $view->Orders()->getRadioButtonsOptions(2);?>
                <form method="POST" action="<?php echo $view->url(array('action'=>'createOrder', 'buyer' => $view->isBuyer));?>">
                    <input type="hidden" value ="<?php echo $view->producto; ?>" id="productoId" name="productoId" />
                <?php
                 //$view->Orders()->printCreateOrder($view->users, $view->isBuyer);
                if($view->getRequest()->getParam('buyer')){
                	$view->Orders()->printCreateOrder($view->users);
				}else{
					$view->Orders()->printCreateOrder($view->users,TRUE);
                }
                
                 ?>
                    <br/>
                    <input id="submitCreateOrder"class="btn-default sin-padding pers-btn icono-guardar tam-normal" type="submit" value=""/>
                </form>
            </div>
        <!--</div>-->
    </div>
</div>
