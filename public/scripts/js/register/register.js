$(document).ready(function ()
{
    initValidate();
});

function initValidate()
{
    
    var urlValidate= $('#urlValidateEmail').val();
    $('#register').validate(
    {
        rules:{
            email:{
                required:true,
                email:true,
                remote:{
                        url:urlValidate,
                        type:"post",
                        data:{
                            email:function(){return $('#email').val();}
                        }
                }
            },
            pass:{
                required:true
            },
            confirmation:{
                required:true,
                equalTo: '#pass'
            }
//            ,
//            firstName:{
//                required:true
//            },
//            lastName:{
//                required:true
//            },
//            company:{
//                required:true
//            },
//            country:{
//                required:true
//            },
//            localNumber:{
//                digits:true
//            },
//            cellPhone:{
//                required:true,
//                digits:true
//            }
        },
        messages:{
            email:{
                required:'Email is required',
                email:'Please insert a valid e-mail',
                remote:'Please register before'
            },
            pass:{
                required:'Password is required'
            },
            confirmation:{
                required:'Confirmation is required',
                equalTo:'Does not match confirmation'
            }
//            ,
//            firstName:{
//                required:'First Name is required'
//            },
//            lastName:{
//                required:'Last Name is required'
//            },
//            company:{
//                required:'Company is required'
//            },
//            country:{
//                required:'Country is required'
//            },
//            LocalNumber:{
//                digits:'Enter only numbers'
//            },
//            cellPhone:{
//                required:'Cellphone is required',
//                digits:'Enter only numbers'
//            }
        }
        ,
        errorPlacement: function(error,element){
            error.appendTo(element.parent().next());
            }

    });
}
