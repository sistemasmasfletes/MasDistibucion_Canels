function ActivityTypeEditController($scope,$timeout,$state,$stateParams,PARTIALPATH,ModalService,CatalogService,ActivityTypeDataService,activityType){
    $scope.activity=activityType;
    $scope.activityTypeStatus=CatalogService.getActivityTypeStatus(); //definir en CatalogService
    $scope.activityTypeTypes=CatalogService.getActivityTypeType();

    $scope.save=save;
	$scope.back=function(){$state.go('^',$stateParams)};


    function save(){
        if($scope.activity){
            $scope.loading=true;
            ActivityTypeDataService.save($scope.activity, {})
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

    $scope.getFormFieldCssClass = function(ngModelController) {
        if(ngModelController.$pristine) return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    }
}