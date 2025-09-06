$(document).ready( function(){
    initValidate();
});
function initValidate()
{
    $('#sendFeedBack').validate(
    {
        rules:{
            feedback:{
                required:true
            }
        },
        messages:{
            feedback:{
                required:'This field is required'
            }
        }
    });
}
