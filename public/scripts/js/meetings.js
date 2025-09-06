$(document).ready( function(){
    showDetailsMeeting();
    showDetailsRespondMeeting();
})
function showDetailsMeeting()
{
      $('.dialogMeeting').click(function()
    {
        /*sacar valor del mansaje*/
        var id=$(this).attr('id');
        var message=$('#urlStatus_id_'+id).val();
        var html='<div>';
        html+='<label>Meeting</label><br/>'+message+'<br/>';
        html+='</div>';
        $('#meetings').html(html);

        $('#meetings').dialog({
            modal:true,
            resizable:false,
            buttons: {
                Aceptar:
                {
                    text:'Accept',
                    click:function() {
                    $(this).dialog('close');
                    }
                }
            },
            open:initDialogButtons,
            closeText: 'hide',
            width:450
        });
      });

}

function showDetailsRespondMeeting()
{
    $('.dialogRespond').click(function()
    {
        /*sacar valor del mansaje*/
        var id=$(this).attr('id');
        var message=$('#urlStatusRespond_id_'+id).val();
        var html='<div>';
        html+='<label>Respond</label><br/>'+message+'<br/>';
        html+='</div>';
        $('#meetings').html(html);

        $('#meetings').dialog({
            modal:true,
            resizable:false,
            buttons: {
                Aceptar:
                {
                    text:'Accept',
                    click:function() {
                    $(this).dialog('close');
                    }
                }
            },
            open:initDialogButtons,
            closeText: 'hide',
            width:450
        });
      });
    
}

function initDialogButtons()
{
    $('.buttonDialog').button();
}

$('.dialogMeeting').click(function()
    {
        var id=$(this).attr('value');
        $('#dialog').dialog({
            modal:true,
            resizable:false,
            buttons: {
                Aceptar:
                {
                    text:'Accept',
                    click:function() {
                        $('#formUpdate').attr('action',$('#urlStatus_'+id).val()); 
                        $(this).dialog('close');
                        $('#formUpdate').submit(); 
                    }
                }
            },
            title:'Compose Message',
            closeText: 'hide',
            width:450
        });
    });


