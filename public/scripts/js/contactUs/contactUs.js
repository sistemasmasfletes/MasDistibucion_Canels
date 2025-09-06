$(document).ready(function ()
    {
        validate();
    });

function validate()
{
    $('#contactUs').validate(
    {
        rules:{
            username:{
                required:true,
                email:true
            },
            telephone:{
                digits:true
            },
            firstName:{
                required:true
            },
            lastName:{
                required:true
            }
        },
        messages:{
            username:{
                required:'Email is required',
                email:'Please insert a valid e-mail'
            },
            firstName:{
                required:'First Name is required'
            },
            lastName:{
                required:'Last Name is required'
            },
            telephone:{
                digits:'Enter only numbers'
            }
        }
        ,
        errorPlacement: function(error,element){
            error.appendTo(element.parent().next());
        }

    });
}

