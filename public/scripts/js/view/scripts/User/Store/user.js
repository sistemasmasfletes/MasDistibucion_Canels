$(document).ready(initEvents);

function initEvents()
{
    $('#addFavoriteBuyer a').click(addToFavoriteBuyer);
    $('#removeFavoriteBuyer a').click(removeFromFavoriteBuyer);
    $('#addFavoriteSeller a').click(addToFavoriteSeller);
    $('#removeFavoriteSeller a').click(removeFromFavoriteSeller);
}

function addToFavoriteBuyer()
{
    var arrId = $(this).attr('id').split('_');
    var typeFavorite = $('#typeFavoriteBuyer').val();
    var clientId = arrId[3];
    
    $.post(urlAddFavorite, {'clientId':clientId,
                            'type':typeFavorite}, function(data)
    {
        if(data == true || data == 'true')
        {
            $('#addFavoriteBuyer').hide();
            $('#removeFavoriteBuyer').show();
            alert('Ha agregado al cliente '+commercialName+' a sus favoritos');
        }
    });
}

function removeFromFavoriteBuyer()
{
    var arrId = $(this).attr('id').split('_');
    var typeFavorite = $('#typeFavoriteBuyer').val();
    var clientId = arrId[2];
    
    $.post(urlRemoveFavorite, {'clientId':clientId,
                                'type':typeFavorite}, function(data)
    {
        if(data == true || data == 'true')
        {
            $('#addFavoriteBuyer').show();
            $('#removeFavoriteBuyer').hide();
            alert('Ha eliminado al cliente '+commercialName+' a sus favoritos');
        }
    });
}

function addToFavoriteSeller()
{
    var arrId = $(this).attr('id').split('_');
    var typeFavorite = $('#typeFavoriteSeller').val();
    var clientId = arrId[3];
    
    $.post(urlAddFavorite, {'clientId':clientId,
                            'type':typeFavorite}, function(data)
    {
        if(data == true || data == 'true')
        {
            $('#addFavoriteSeller').hide();
            $('#removeFavoriteSeller').show();
            alert('Ha agregado al proveedor '+commercialName+' a sus favoritos');
        }
    });
}

function removeFromFavoriteSeller()
{
    var arrId = $(this).attr('id').split('_');
    var typeFavorite = $('#typeFavoriteSeller').val();
    var clientId = arrId[2];
    
    $.post(urlRemoveFavorite, {'clientId':clientId,
                                'type':typeFavorite}, function(data)
    {
        if(data == true || data == 'true')
        {
            $('#addFavoriteSeller').show();
            $('#removeFavoriteSeller').hide();
            alert('Ha eliminado al proveedor '+commercialName+' a sus favoritos');
        }
    });
}