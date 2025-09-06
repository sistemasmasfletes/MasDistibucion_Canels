<?php
$dbs = Model3_Registry::getInstance()->get('databases');
$em = $dbs['DefaultDb'];
$productsAdapter = $em->getRepository('DefaultDb_Entities_Product');
$user = $view->user;

if($user instanceof DefaultDb_Entities_User )
{
    $status = $user->getStatus();
    ?>
    <div class="accordion" id="accordion2" style="font-size: 14px; font-weight: bold;">
        Status actual:
        <br>
        <?php ?>
        <div class="btn-group" id="userStatus" idUser="<?php echo $user->getId();?>" data-toggle="buttons-radio">
            <button class="btn btn-primary<?php echo $status == DefaultDb_Entities_User::STATUS_INACTIVE?' active':'';?>"
                    value="<?php echo DefaultDb_Entities_User::STATUS_INACTIVE;?>">Inactivo</button>
            <button class="btn btn-primary<?php echo $status == DefaultDb_Entities_User::STATUS_ACTIVE?' active':'';?>"
                    value="<?php echo DefaultDb_Entities_User::STATUS_ACTIVE;?>">Activo</button>
            <button class="btn btn-primary<?php echo $status == DefaultDb_Entities_User::STATUS_BLOCK?' active':'';?>"
                    value="<?php echo DefaultDb_Entities_User::STATUS_BLOCK;?>">Bloqueado</button>
        </div>
    </div>
<textarea id="comment" placeHolder=" ..." style="max-width:300px !important; max-height: 300px !important;" ></textarea>
    <div id="tableComments">
    <?php 
    if($view->comments)
    {
        ?>
    
        <table class="table">
            <tr>
                <th style="border-right: none !important;border-left: none !important;">Comentario</th>
                <th style="border-right: none !important;border-left: none !important;">Actualización</th>
                <th style="border-right: none !important;border-left: none !important;">Fecha de cambio</th>
            </tr>
        <?php
        foreach($view->comments as $comment): ?>
           <tr >
               <td style="border-right: none !important;border-left: none !important;"><?php echo $comment->getComment();?></td>
               <td style="border-right: none !important;border-left: none !important;"><?php 
                               switch ($comment->getNewStatus()){
                                   case DefaultDb_Entities_User::STATUS_INACTIVE:
                                       echo 'Inactivo';
                                       break;
                                   case DefaultDb_Entities_User::STATUS_ACTIVE:
                                       echo 'Activo';
                                       break;
                                   case DefaultDb_Entities_User::STATUS_BLOCK:
                                       echo 'Bloqueado';
                                       break;
                               }
               ?></td>
               <td style="border-right: none !important;border-left: none !important;"><?php echo $comment->getDateChange()->format('Y-m-d');?></td>
           </tr>
       <?php 
       endforeach; ?>
        </table>
    </div>
    <?php 
    } 
}
else
{
    echo '<div class="alert">No se ha encontrado informacion para este usuario</div>';
}
?>

