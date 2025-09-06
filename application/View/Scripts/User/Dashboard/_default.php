<div class="container">
    <?php
    if (count($view->user[0]->getBranches()) > 0){
        ?>
        <div class="alert alert-info ">
            <a class="close" data-dismiss="alert">x</a>
            Ahora puedes ver las tiendas de tus proveedores y tus clientes tendran la oportunidad de ver tus productos
        </div>
    <?php 
        if($view->branchesPending>0){
            ?>
            <div class="alert alert-info ">
            <a class="close" data-dismiss="alert">x</a>
            Tienes sucursales pendientes de aprobar por el controlador de operaciones.
            </div>
        <?php
        }
    }
    else{
        ?>
        <div class="alert">
            Por el momento no tienes sucursales activas y tus clientes no podran ver tus productos ni tu tienda
        </div>
    <?php
    }
    ?> Bienvenido <?php
    echo $view->user[0]->getFullName();
    ?>
</div>