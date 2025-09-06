function VehiclesEditController($scope,$timeout,$state,$stateParams,PARTIALPATH,ModalService,CatalogService,VehiclesDataService,vehicle){
    $scope.vehicle=vehicle;
    $scope.vehicleStatus=CatalogService.getVehicleStatus();
    $scope.vehicleTypes=CatalogService.getVehicleType();

    $scope.save=save;
	$scope.back=function(){$state.go('^',$stateParams)};


    function save(){
        if($scope.vehicle){
            $scope.loading=true;
            VehiclesDataService.save($scope.vehicle, {})
                .success(function(data, status, headers, config){
                    $scope.loading=false;
                    if (data.error) {
                         var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: data.error
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions);
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

    $scope.getFormFieldCssClass = function(ngModelController) {
        if(ngModelController.$pristine) return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    }
}