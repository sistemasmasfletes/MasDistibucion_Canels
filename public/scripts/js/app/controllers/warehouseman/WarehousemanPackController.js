function WarehousemanPackController ($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,WarehousemanDataService,UtilsService,CONFIG){
    
    var modalInfoPath = PARTIALPATH.modalInfo
    
    var $routeId = $stateParams.routeId;
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.scheduledDate = $scope.$parent.selScheduledDate;
    $scope.currentData = [];
    $scope.grid={};
    $scope.grid.transfer = function(){grdTransfer($scope.rpaId)};
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:100,
            sorting:{
                id:'asc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = UtilsService.createNgTablePostParams(params,{scheduleRouteId:$stateParams.scheduleRouteId});
                WarehousemanDataService.getWarehousePacks(postParams)
                .then(function(response){
                    $scope.currentData=response.data;
                    $scope.isLoading=false;
                    params.total($scope.currentData.meta.totalRecords);
                    $defer.resolve($scope.currentData.data[0]);
                });       
            }
        }

    );
    
    $scope.changeSelection = function(schedule) {
        var data = $scope.tableParams.data;
        /*var $scheduleRouteId = schedule.scheduleRouteId;
        var $routePointId = schedule.routePointId;
        var $orderId = schedule.orderId;
        var $routePointActivityId = schedule.rpaId;*/
        for(var i=0;i<data.length;i++){
            if(data[i].id!=schedule.id)
                data[i].$selected=false;
        }
        /*if(schedule){
            $state.go('warehouseman.form',{scheduleRouteId:$scheduleRouteId, routePointId:$routePointId,orderId:$orderId, routePointActivityId:$routePointActivityId});
        }*/
    }
    
    $scope.go = function(schedule){
        var data = $scope.tableParams.data;
        var $routeId = schedule.routeId;
        var $scheduledRouteId = schedule.scheduleRouteId;
        var $routePointId = schedule.routePointId;
        if(schedule){
            $state.go('warehouseman.activity',{routeId:$routeId, scheduleRouteId:$scheduledRouteId, routePointId:$routePointId});
        } else {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: '¡Seleccione un registro para poder continuar!'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){});
        }
    }
    
    /*function grdTransfer(id,schedule){
        console.log(id);
        var $scheduleRouteId = schedule.scheduleRouteId;
        var $routePointId = schedule.routePointId;
        var $orderId = schedule.orderId;
        var $routePointActivityId = schedule.rpaId;
        if($routePointActivityId)
           $state.go('warehouseman.form',{scheduleRouteId:$scheduleRouteId, routePointId:$routePointId, orderId:$orderId, rpaId:$routePointActivityId}); 
        else{
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Debe seleccionar un paquete para poder realizar la transferencia.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        }
    }*/
    
    $scope.back=function(){
        $state.go('warehouseman.point',{routeId:$routeId}); //NAVEGA A PANTALLA DE PUNTOS DE LA RUTA
    }

}