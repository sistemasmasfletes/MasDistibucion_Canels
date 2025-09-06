function CausesEditController($scope,$timeout,$state,$stateParams,PARTIALPATH,ModalService,CatalogService,CausesDataService,causes){
    $scope.cause=causes;
    $scope.causeStatus=CatalogService.getCauseStatus();
    
    $scope.save=save;
    $scope.back=function(){$state.go('^',$stateParams)};

    function save(){        
        if($scope.cause){
            $scope.loading=true;
            CausesDataService.save($scope.cause, {})
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