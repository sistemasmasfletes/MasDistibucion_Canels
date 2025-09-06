$(document).ready(function()
{
    $('#alert').hide();
    $('#alert-error').hide();
    $('#salePointTypeSelector').change(function()
    {
        changeSalePointTypeSelector();
        
    });
    
    $('#submitter').click(function()
    {
        var salePointType = $('#salePointTypeSelector').val();
        var postData ;
        
        switch(salePointType)
        {
            case '1':
                postData = {
                    routeId: routeId,
                    creationType: 1,
                    pointCode: $('#pointCode').val(),
                    pointName: $('#pointName').val(),
                    pointType: $('#pointType').val(),
                    pointAddress: $('#pointAddress').val(),
                    state:$('#state').val(),
                    routePointArrivalTime: $('#routePointArrivalTime').val()
                }
                break;

            case '2':
                postData = {
                    routeId: routeId,
                    creationType: 2,
                    pointId: $('#existentSalePointSelector').val(),
                    routePointArrivalTime: $('#routePointArrivalTime').val()
                }
                break;
                
            case '3':
                postData = {
                    routeId: routeId,
                    creationType: 3,
                    pointId: $('#existentExchangePointSelector').val(),
                    routePointArrivalTime: $('#routePointArrivalTime').val()
                }
                break;
            case '4':
                postData = {
                    routeId: routeId,
                    creationType: 4,
                    pointId: $('#allExistentExchangePointSelector').val(),
                    routePointArrivalTime: $('#routePointArrivalTime').val()
                }
                break;
        }
        $.post(newRoutePoint, postData, function(data)
        {
            if(data.result)
            {
                $('#pointCode').val('');
                $('#pointAddress').val('');
                $('#pointName').val('');
                $('#pointType').val('0');
                $('#pointAddress option[value="0"]').attr("selected", true);
                $('#state option[value="0"]').attr("selected", true);
                $('#routePointArrivalTime').val('');
                //Recargar nuevamente los centros de intercambio
    //            $.post(urlRechargeInterchangeCenter, data, function(data){
    //                $('#existentExchangePointSelector').html(data);
    //            });
                $('#alert').show();
            }
            else
            {
                $('#alert-error').show();
            }
        },'json');
       
    });
    changeSalePointTypeSelector();
});

function changeSalePointTypeSelector()
{    
    switch($('#salePointTypeSelector').val())
    {
        case '1':
            $('#existentSalePointContainer').hide();
            $('#existentExchangePointContainer').hide();
            $('#newSalePointContainer').show();
            $('#allExistentExchangePointContainer').hide();
            break;

        case '2':
            $('#newSalePointContainer').hide();                
            $('#existentExchangePointContainer').hide();
            $('#existentSalePointContainer').show();
            $('#allExistentExchangePointContainer').hide();
            break;

        case '3':
            $('#newSalePointContainer').hide();
            $('#existentSalePointContainer').hide();
            $('#existentExchangePointContainer').show();
            $('#allExistentExchangePointContainer').hide();
            break;
            
        case '4':
            $('#newSalePointContainer').hide();
            $('#existentSalePointContainer').hide();
            $('#existentExchangePointContainer').hide();
            $('#allExistentExchangePointContainer').show();
            break;
    }
}