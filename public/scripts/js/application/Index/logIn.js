$(document).ready(function(){

    $('#logIn').click(function(){
        if($("#formLogIn").valid())
        {
            logIn();
        }
    });
});

function initValidator()
{
   $("#formLogIn").validate({
        rules: {
            correo: {
                required:true
            },
            password: {
                required:true
            }
        },
        messages: {
            correo: {
                required:'campo requerido'
            },
            password: {
                required:'campo requerido'
            }
        }
    });
}



function logIn()
{
    $('#loaderLogIn').show();
    var user = $('#correo').val();
    var pass = $('#password').val();
    $.post(logInURL, {
        user: user,
        pass: pass
    }, function(data){
        $('#loaderLogIn').hide();
        if(data.res)
        {
            $('#responseLogIns').html('<div class="alert alert-success">'+data.message+'</div>');
            window.location.href = data.url;
        }
        else
        {
            $('#responseLogIns').html('<div class="alert alert-error">'+data.message+'</div>');
        }

    }, 'json');

    $('#responseLogIns').show(500);
    setTimeout(function(){
        $("#responseLogIns").hide(500);
    }, 3000);
}