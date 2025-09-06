
function deleteUser(idUser)
{
    var url = $('#url-post-del').val();
    if(confirm('Delete user?'))
    {
        $.post(url,{idUser:idUser},function(data){            
            $('#usr-container-'+idUser).remove();
        });
    }
}