<?php /*@var $view Model3_View*/ ?>
<?php 
if(count($view->points) > 0): 
    foreach($view->points as $point): ?>
    <div class="userFinalSuggestList" >
    
        <div style="display: block"><?php echo $point['name']; ?></div>
        <div style="display: block"><?php echo $point['address']; ?></div>
        <div style="display: block"><?php echo $point['usrname']; ?></div>
        <input class="point_id" type="hidden" value="<?php echo $point['buid']; ?>" />
        <input class="point_address" type="hidden" value="<?php echo $point['address']; ?>" />    
        
        <!-- div style="display: block"><?php //echo $point->getName(); ?></div>
        <div style="display: block"><?php //echo $point->getFullAddress(); ?></div>
        <div style="display: block"><?php /*echo ($point->getAddress() 
                                                ?   ($point->getAddress()->getState() 
                                                        ? $point->getAddress()->getState()->getName() : ''
                                                    )
                                                :''
                                                );*/ ?></div>
        <input class="point_id" type="hidden" value="<?php //echo $point->getId(); ?>" />
        <input class="point_address" type="hidden" value="<?php //echo $point->getFullAddress(); ?>" /-->
        
    </div>
  <?php endforeach; ?>
<?php else: ?>
<div class="alert alert-info" style="margin-top: 18px;">Sin coincidencias</div>
<?php endif;