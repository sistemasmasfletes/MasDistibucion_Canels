$(document).ready( function(){

    $('#sfAddToCartButton').click(function(){
        var sfVariantId=null;
        var selectVariant='#variantes_'+sfItemId;
        if($(selectVariant).length>0)
        {
            sfVariantId = $(selectVariant).val();
        }
        $.ajax({
            url: urlAddToCart,
            type: "POST",
            data: {
                id:sfItemId,
                idVariant:sfVariantId
            },
            success: function(data){
                if(data == false)
                {
                    $('#messages').append('<div class="alert alert-error">Ya existen productos en su carrito de otra Tienda, termine el proceso de compra o vacie el carrito.</div>');
                }
                else
                {
                    $('#messages').append('<div class="alert alert-success">Se añadio producto al carrito</div>');
                }
                setTimeout(function(){
                    $('#messages').html('');   
                }, 5500);
                location.reload(true);

            }
            
        });
    })
    
    $('#sfAddCommentsCart').click(function(){
        $.ajax({
            url: urlAddCommentsCart,
           type: "POST",
           data:{
               idStore : sfIdStores
           },
        success:function(data){
            if(data == false)
            {
                $('#messages').append('<div class="alert alert-error">Ya existen productos en su carrito de otra Tienda, termine el proceso de compra o vacie el carrito.</div>');
            }
            else
            {
                alert("Escribe tu pedido");
            }
        }
    })
    });
});


function addFavoriteToCar(productId,carDetails){
    
    var sfVariantId=null;    
    console.log(urlAddToCart);
    $.ajax({
            url: urlAddToCart,
            type: "POST",
            data: {
                id:productId,
                idVariant:sfVariantId
            },
            success: function(data){
                if(data == false)
                {
                    $('#messages').append('<div class="alert alert-error">Ya existen productos en su carrito de otra Tienda, termine el proceso de compra o vacie el carrito.</div>');
                }
                else
                {
                    $('#messages').append('<div class="alert alert-success">Se añadio producto al carrito</div>');
                }
                setTimeout(function(){
                    $('#messages').html('');                 
                }, 5500);
                location.href = carDetails;

            }
            
        });
}
