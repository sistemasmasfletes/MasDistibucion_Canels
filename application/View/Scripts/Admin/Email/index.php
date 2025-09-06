<?php /*@var $this Model3_View*/ ?>
<script type="text/javascript">
    $(document).ready(function(){
       $('.status').live('click',function(){
           var id=$(this).attr('id');
           id=id.split('_');
           $('#loaderImage').show();
           $.post(url,{idCliente:id[1],status:status[0]},function(data){
               $('#loaderImage').hide();
               if(data==true)
                {
                    $('#'+id[0]+'_'+id[1]).addClass('active');
                    if(id[0]=='statusFalse')
                        $('#'+id[0]+'_'+id[1]).next().removeClass('active');
                    else
                        $('#'+id[0]+'_'+id[1]).prev().removeClass('active');
                }
           });
       });
    });
</script>
<div class="contiene-bread">
    <ol class="breadcrumb">
    <li class="active">Sistema</li>
    <li class="active actualpg ">Correos</li>
</ol>
</div>
<div id="container" class="container">
    <div class="row">
        <!--<div class="span12 blockGray">-->
            <div class="blockInner">
                <h2>Configuracion de correo</h2>
<!--                <div class="row span12">
                    <a class="btn" href="<?php //echo $view->url(array('action' => 'index')); ?>" >Regresar</a>
                </div>-->
                <div style="clear: both;">&nbsp;</div>
<!--                <div class="row span11">
                    <div class="well">
                        <form class="form-inline">
                            <input type="text" class="input-small" placeholder="Email">
                            <button type="submit" class="btn">Agregar</button>
                        </form>
                    </div>
                </div>-->
<!--                <div style="clear: both;">&nbsp;</div>-->
                <div class="row span11">
                    <div class="well">
                        <table class="table table-striped table-condensed">
                            <tr>
                                <th>Tipo</th>
                                <th>Email</th>
                                <th>Opciones</th>
                            </tr>
                            <?php foreach ($view->receptionists as $key => $receptionist):?>
                            <tr>
                                <td><?php echo $receptionist->getTypeString();?></td>
                                <td><?php echo $receptionist->getUsername();?></td>
                                <td>
                                    <div class="btn-group">
                                        <button id="statusFalse_<?php echo $receptionist->getId()?>" class="status btn btn-primary <?php echo ($receptionist->getStatus()!=DefaultDb_Entities_User::STATUS_ACTIVE?'active':'');?>" >Deshabilitar</button>
                                        <button id="statusTrue_<?php echo $receptionist->getId()?>" class="status btn btn-primary <?php echo ($receptionist->getStatus()==DefaultDb_Entities_User::STATUS_ACTIVE?'active':'');?>" >Habilitar</button>
                                    </div>
                                    <?php echo $view->ManagerImages()->img('images/loading.gif','loading','loading','id="loaderImage" class="hide"');?>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </table>
                    </div>
                </div>
            </div>
        <!--</div>-->
    </div>
</div>
