$(document).ready( function(){    
    $('.pagLink').live('click', function(){
        $("#listUser").html('<div style="text-align:center; margin: 20px;"><img src="'+ajaxLoader+'" /></div>');
        $("#listUser").load(urlList, {
            page:$(this).attr('id').substring(5), 
            rows:$('#rows').val()
        });
        $.cookie('pageNo', $(this).attr('id').substring(5), {
            expires: 7, 
            path: '/'
        })    
    });
    $('.addFav').live('click', function(){
        $(this).parent().load(urlAddFavorite, {
            id:$(this).attr('id').substring(3)
        });        
    });
    $('#searchBtn').click(function(){
        $("#listUser").html('<div style="text-align:center; margin: 20px;"><img src="'+ajaxLoader+'" /></div>');
        $("#listUser").load(urlSearch, {
            search:$('#search').val()
        });
    });
    $('#rows').change(function(){
        $("#listUser").html('<div style="text-align:center; margin: 20px;"><img src="'+ajaxLoader+'" /></div>');
        $("#listUser").load(urlList, {
            rows:$('#rows').val()
        });
        $.cookie('rowsNo', $('#rows').val(), {
            expires: 7, 
            path: '/'
        })        
    });
    
    /**
     * Inicializamos con cookies
     */
    pageNo = $.cookie('pageNo');
    if(pageNo == null)
        pageNo = 1;
    
    rowsNo = $.cookie('rowsNo');
    if(rowsNo == null)
        rowsNo = 25;
    else
        $('#rows').val(rowsNo);    
    
    $("#listUser").load(urlList, {
        page:pageNo, 
        rows:rowsNo
    });
});