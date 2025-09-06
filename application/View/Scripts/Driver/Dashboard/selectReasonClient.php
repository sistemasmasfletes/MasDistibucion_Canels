<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <?php 
                if($view->result==true){
                    echo 'Cliente: ' . $view->user->getFullName(); 
                }
                else{
                    echo 'Clave de cliente incorrecto'; 
                } 
                ?>
            </div>
        </div>
        <br/>
        <div class="span12 blockGray">
            <div class="row">
            <?php if($view->result):?>
                <div class="span4 btnDiv">
                    <a class="btn" href="<?php echo $view->url(array('module'=>'Driver', 'controller'=>'FavoriteUsers', 'action'=>'index')); ?>">Visita</a>
                </div>
                <div class="span4 btnDiv">
                    <a class="btn" href="<?php echo $view->url(array('module'=>'User', 'controller'=>'BackStore', 'action'=>'orders')); ?>">Recolecci&oacute;n</a>
                </div>
                <div class="span4 btnDiv">
                    <a class="btn" href="<?php echo $view->url(array('module'=>'User', 'controller'=>'BackStore', 'action'=>'shopping')); ?>">Entrega</a>
                </div>
            <?php else:?>
            <div class="span4 ">
                <a class="btn" href="<?php echo $view->url(array('module'=>'Driver', 'controller'=>'Dashboard')); ?>">Regresar</a>
            </div>
            <?php endif;?>
            </div>
        </div>
    </div>
</div>