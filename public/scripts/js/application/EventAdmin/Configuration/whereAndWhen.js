$(document).ready( function(){
    submitForm();
  //  initValidate();
});
function submitForm()
{
    var url = $('#url').val();
    $('#submitWhereAndWhen').live('click',function(){
    initValidate();
    $.post(url,{
        textWhereAndWhen:$('#textWhereAndWhen').val()
    },function(data){
        if(data == 1)
        {
            $('#message').show(300);
        }
        else
        {
            $('#message').html('<div class="error">Error</div>');
            $('#message').show(300);
        }
    });
    })
}
function initValidate()
{
    $('#formWhereAndWhen').validate(
    {
        rules:{
            textWhereAndWhen:
            {
                required:true
            }
        },
        messages:{
            textWhereAndWhen:
            {
                required:'This field is required'
            }
        }
    });
}
