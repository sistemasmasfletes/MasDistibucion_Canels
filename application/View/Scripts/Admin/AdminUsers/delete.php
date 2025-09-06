<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'index')) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->user):?>
                <div class="alert alert-success">
                El usuario <?php echo $view->user->getFullName();?> se elimino correctamente
                </div>
                <?php else: ?>
                <div class="alert alert-error">
                El usuario <?php echo $view->user->getFullName();?> no se pudo eliminar por favor intentelo mas tarde
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>