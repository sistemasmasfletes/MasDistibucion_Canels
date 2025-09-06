<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Finanzas</li>
    <li class="active actualpg ">Órdenes</li>
</ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="blockGray">-->
            <div class="blockInner">
                <h1>
                    Busqueda de ordenes
                </h1>
                <div class="con">
                    <div class="">
                        <form method="post" action="#">
                            N&uacute;mero de orden: <input type="text" name="numOrden">
                            <button type="submit" class="btn btn-default sin-padding">
                                <span class="pers-btn icono-buscar tam-normal"></span>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div id="reportResult">
                    <?php
                        if($view->order instanceof DefaultDb_Entities_M3CommerceOrder )
                        {
                            $view->Orders()->printOrderRepord($view->order);
                        }
                        else
                        {
                            echo '<div class="alert">No se ha encontrado la orden. Inidique el numero de pedido, y de clic en buscar.</div>';
                        }
                        
                    ?>
                </div>
            </div>
        <!--</div>-->
    </div>
</div>