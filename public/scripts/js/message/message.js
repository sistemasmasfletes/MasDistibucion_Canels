$(document).ready( function(){
    showDetailsMessages();
    deleteMessage();
    unreadMessage();
    respondMessage();
})
function showDetailsMessages()
{
      $('.dialogMessage').live('click',function()
    {
        /*sacar valor del mansaje*/
        var id=$(this).attr('id');
        var message=$('#urlStatus_id_'+id).val();
        var guest = $('#idGuest_id_'+id).val();
        var html='<div>';
        html+='<label>Message</label><br/>'+message+'<br/>';
        html+='</div>';
        $('#messages').html(html);

        $('#messages').dialog({
            modal:true,
            resizable:false,
            buttons: {
                Aceptar:
                {
                    text:'Accept',
                    click:function() {
                    $.post($('#urlUpdateStatus_id_'+id).val());
                    $(this).dialog('close');
                    }
                }
//                ,
//                Respond:
//                {
//                    text:'Respond',
//                    click: function(){
//                        $('#answer').dialog({
//                            buttons:{
//                                Send:function()
//                                {
//                                    $('#formUpdateMessage').attr('action',$('#urlRespondMessage_id_'+id).val()); 
//                                    $(this).dialog('close');
//                                    $('#formUpdateMessage').submit(); 
//                               }
//                            }
//                        })
//                    }
//                }
            },
            closeText: 'hide',
            width:450
        });
      });

}

function respondMessage()
{
    $('.respondMessage').live('click',function()
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
                        $('#formUpdateMessage').attr('action',$('#urlRespondMessage_'+id).val()); 
                        $(this).dialog('close');
                        $('#formUpdateMessage').submit(); 
                    }
                }
            },
            title:'Compose Message',
            closeText: 'hide',
            width:450
        });
    });
    
    
}

function deleteMessage()
{
    $('.deleteMessage').live('click',function()
    { 
        var id=$(this).attr('id');
        var url = $('#urlDeleteMessage_id_' + id).val();
        if(confirm('Sure to delete the message?'))
            $.post(url + id); 



    });
}

function unreadMessage()
{
    $('.unreadMessage').live('click',function()
    { 
        var id =  $(this).attr('id');
        var baseUrl = $('#urlbase').val();
        var url = $('#urlUnreadMessage_id_'+ id).val();
        if(confirm('Sure to change the status?'))
            $.post(url);
            location.href = baseUrl;
    });
}

