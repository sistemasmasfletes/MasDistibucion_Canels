<div class="container">
    <div class="row" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                Bienvenido Administrador <br/><br/>
                <?php 
                if($view->branchesPending)
                {
                    echo '<div class="alert"> <a class="close" data-dismiss="alert">x</a>';
                    echo 'Existen '.count($view->branchesPending).' nuevas solicitudes de sucursales para asignarlas de clic 
                        <a href="'.$view->getBaseUrl().'/Admin/ReportsUsers/branchesPending/" > aqu&iacute;</a>
                        </div>';
                   
                }       
                
                if($view->pendingAddress)
                {
                    echo '<div class="alert"> <a class="close" data-dismiss="alert">x</a>';
                    echo 'Existen '.count($view->pendingAddress).' direcciones que requieren autorización, para autorizarlas dirigasé al catálogo de Direcciones.
                        </div>';
                   
                }     
                ?>
            </div>
        <!--</div>-->
    </div>
</div> 