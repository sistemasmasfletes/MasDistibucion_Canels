$(document).ready( function(){
    $('#listUser').load(userUrl);
    $('.pagLink').live('click', function(){
        $('#listUser').html('<div style="text-align:center; margin: 20px;"><img src="'+ajaxLoader+'" /></div>');
        $('#listUser').load(userUrl, {page: $(this).attr('id').substring(5)});
    })
    
    $('#searchBtn').click(function(){
       $("#listUser").html('<div style="text-align:center; margin: 20px;"><img src="'+ajaxLoader+'" /></div>');
       $("#listUser").load(urlSearch, {search:$('#search').val()});         
    });
});
