/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



    function uploadFile(image) {
    var blobFile = $('#filechooser')[0].files[0];
//    var files = blobFile[0].files[0];
    var formData = new FormData();
    formData.append("fileToUpload", blobFile);

    $.ajax({
       url: urlSaveImage,
       type: "POST",
       data: formData,
       processData: false,
       contentType: false,
       success: function(response) {
           document.getElementById("imagePath").value  = 'data/images/categorias/' + blobFile.name;
           $('#filechooser').prop( "disabled", true );
       },
       error: function(jqXHR, textStatus, errorMessage) {
           console.log(errorMessage); // Optional
       }
    });
}