<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Clientes</li>
        <li class="active "> <?php
            echo '<a href="' . $view->url(array('action' => 'index')) . '" >Clientes Favoritos</a>';
            ?></li>
        <li class="active actualpg ">Eliminar Cliente</li>
    </ol>
</div>
<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <?php if ($view->res == true ) : ?>
                <div class="alert alert-success">
                    Se ah eliminado la tienda de tus favoritos
                </div>
                <?php endif; ?>
            </div>
        <!--</div>-->
    </div>
</div>