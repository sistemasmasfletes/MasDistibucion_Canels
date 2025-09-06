/**
 * Script para el Admin_AdminUsersController => addAction
 * Add.js
 */
$(document).ready(function(){
    
    $('#type').live('change',function(){
        var type = $(this).val();
        if(type == 3)
        {
            $.post(urlTypeClient,{},function(data){
                $('#client').show();
                $('#client').html(data);
            });
        }
        else
        {
            $('#client').hide();
        }
    });
     
    changeRoute();
});

function changeRoute()
{
    $('.route').live('change',function(){
        id = $(this).attr('id');
        id = id.split('_');
        id = id[1];
        var route = $(this).val();
        $.post(changeR,{
            'route' : route
        },function(data){
            $('#point_'+id).html(data);          
        }); 
    });
}