
$(function () {
    $('#file_upload').fileUploadUI({
        uploadTable: $('#files'),
        buildUploadRow: function (files, index, handler)
        {
            return $('<tr><td>' + files[index].name + '<\/td>' +
                    '<td class="file_upload_progress"><div><\/div><\/td>' +
                    '<td class="file_upload_cancel">' +
                    '<button class="ui-state-default ui-corner-all" title="Cancel">' +
                    '<span class="ui-icon ui-icon-cancel">Cancel<\/span>' +
                    '<\/button><\/td><\/tr>');
        },
        buildDownloadRow: function (file, handler) 
        {
            var message = '';
            if(file.successImg != undefined)
            {
                message = '<div class="success">La imagen '+file.name+' ha sido cargada correctamente</div>';
            }

            if(file.errorImg != undefined)
            {
                message = '<div class="error">La imagen '+file.name+'no se ha podido cargar</div><br />';
            }

            $('#notificacionesImagenes').html($('#notificacionesImagenes').html()+message);
            $('#notificacionesImagenes').show(300);
            setTimeout(function()
            {
                $('#notificacionesImagenes').hide(300);
                $('#notificacionesImagenes').html('');
            }, 8000);

            getProductImages();
            return '';
        }
    });
});

function getProductImages()
{
    var idProd = $('#idProd').val();
    
    $.post(urlGetImages,{'idProduct' : idProd},function(data){
        $('#galeryImages').html(data);
        });
}

function deleteImage(imageid){
    $('#notificacionesImagenes').html("Eliminando imagen...");
    $('#notificacionesImagenes').show();
    $.post(urlDeleteImage,{'imageid' : imageid},function(data){
        if(data){
            if(data.success==true){
               getProductImages();
               $('#notificacionesImagenes').hide();
            }else{
                $('#notificacionesImagenes').html($('#notificacionesImagenes').html()+"Ocurrió un error al intentar eliminar la imagen.");
                $('#notificacionesImagenes').show(300);
            }  
        }        
    },
    'json'
    );
}