function AprobacionCreditosEditController($scope,$timeout,$state,$stateParams,PARTIALPATH,ModalService,CatalogService,AprobacionCreditosDataService,aprobacionCreditos){
    $scope.aprobacion=aprobacionCreditos;
    $scope.aprobacionCreditosStatus=CatalogService.getAprobacionCreditosStatus(); //definir en CatalogService
    $scope.aprobacionCreditosTypes=CatalogService.getAprobacionCreditosType();

    $scope.save=save;
	$scope.back=function(){$state.go('^',$stateParams);};
        

    function save(){
        
        if($scope.aprobacion){
            $scope.loading=true;
            AprobacionCreditosDataService.save($scope.aprobacion, {})
                .success(function(data, status, headers, config){
                    $scope.loading=false;
                    if (data.error) {
                         var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: data.error
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
                    } else {
                        var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: '¡Registro guardado con éxito!'
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                                $scope.back();
                                $scope.grid.refresh();
                            });
                        }
                })
                .error(function(data, status, headers, config){
                    $scope.loading = false;                    
                });
        }
    }
    
    $(document).ready(function () {
        estatus();
    });
    function estatus() {
        $.ajax({
            type: 'GET',
            data: {metodo: 'estatus'},
            url: '/MasDistribucion/public/OperationController/AprobacionCreditos/estatus',
            dataType: "json",
            success: ListaEstatus
        });
    }
    function ListaEstatus(data) {
        $('#estatus option').remove();
        var list = data == null ? [] : (data.estatus instanceof Array ? data.estatus : [data.estatus]);

        if (list.length < 1) {
            alert("SIN NINGÚN RESULTADO EN LA BD");
        } else {

            $('#estatus').append('<option value="0">Seleccionar...</option>');
            $.each(list, function (estatus, est) {
                $('#estatus').append('<option value="' + est.estatu + '">' + est.estatu + '</option>');
            });
            $('#estatus').val($scope.compra.estatu);
          
            $('#estatus').focus();
        }
    }
    

    $scope.getFormFieldCssClass = function(ngModelController) {
      //  if(ngModelController.$pristine) return "";
      //  return ngModelController.$valid ? "has-success" : "has-error";
    }
}