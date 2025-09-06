function ClassificationEditController ($scope,$timeout,$state,$stateParams,PARTIALPATH,ModalService,CatalogService,PointsDataService,classification,CONFIG){
    
    $scope.classification = classification;
    $scope.save=save;
    $scope.back=function(){$state.go('^',$stateParams)};
    $scope.getSize = CatalogService.getSize();
    $scope.getActivity = CatalogService.getActivity();
    $scope.getConsumption = CatalogService.getConsumption();
    
    function save(){
        if($scope.classification){
            $scope.loading=true;
            $scope.classification.pointId = $stateParams.pointId;
            PointsDataService.saveClasiffication($scope.classification, {})
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
                                $scope.tableParams.reload();
                            });
                        }
                })
                .error(function(data, status, headers, config){
                    $scope.loading = false;
                });
        }
    }
}