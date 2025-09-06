function WarehousemanSaveTransferController($scope,$timeout,$state,$stateParams,PARTIALPATH,ModalService,CatalogService,WarehousemanDataService,warehouseman,CONFIG){
    $scope.warehouse=warehouseman;          //$scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,CatalogService,RouteSummaryDataService,routeSummary,CONFIG
    $scope.transferType = CatalogService.getTransferStatus();
    
    var $routeId=$stateParams.routeId;
    var $scheduleRouteId = $stateParams.scheduleRouteId;
    var $routePointId = $stateParams.routePointId;
    CatalogService.getCatalogUsers()
        .then(function(response){
            $scope.getUserDelivery = response.data;
         });
    
    $scope.save=save;
    $scope.regresar=function(){
        $state.go('warehouseman.activity',{scheduleRouteId:$scheduleRouteId,routePointId:$routePointId});
    }
    
    function save(){        
        if($scope.warehouse){
            $scope.loading=true;
            if($scope.warehouse.userDelivery==='') $scope.warehouse.userDelivery=null;
            if($scope.warehouse.userReceiving==='') $scope.warehouse.userReceiving=null;
            WarehousemanDataService.save($scope.warehouse, {})
                .success(function(data, status, headers, config){
                    $scope.loading=false;
                    if (data.error) {
                         var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: data.error
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions);
                    } else {
                        var modalOptions2 = {
                            actionButtonText: 'Aceptar',
                            bodyText: '¡Transferencia realizada con éxito!'
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result) {
                                $scope.regresar();
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
