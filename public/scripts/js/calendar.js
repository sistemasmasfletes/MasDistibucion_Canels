
$(document).ready( function(){
    $('#meetingDate').datepicker({dateFormat: 'yy-mm-dd'});
    $('#meetingDate').click(function(){
        $('#ui-datepicker-div').css('z-index', 2);
    });

});


