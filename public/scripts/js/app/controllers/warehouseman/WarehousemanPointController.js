function WarehousemanPointController($scope,$location,$anchorScroll,$timeout,$state,$stateParams,ngTableParams,ModalService,WarehousemanDataService,UtilsService,CONFIG){


    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.scheduledDate = $scope.$parent.selScheduledDate;
    $scope.currentData = [];
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                id:'asc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = UtilsService.createNgTablePostParams(params,{routeId:$stateParams.routeId});
                WarehousemanDataService.getWarehouse(postParams)
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
        for(var i=0;i<data.length;i++){
            if(data[i].id!=schedule.id)
                data[i].$selected=false;
        }
    }
    
    $scope.go = function(schedule){
        var data = $scope.tableParams.data;
        var $routeId = schedule.routeId;
        var $scheduledRouteId = schedule.scheduledRouteId;
        
        console.log($routeId);
        console.log($scheduledRouteId);
        if(schedule){
            $state.go('warehouseman.pack',{routeId:$routeId, scheduleRouteId:$scheduledRouteId});
            //alert($state.go);
        } /*else {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: '¡Seleccione un registro para poder continuar!'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){});
        }*/
    }
    
    $scope.getDateFromString = function(dateString){        
        return UtilsService.getDateFromString(dateString);
    }   
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    
    $scope.back=function(){
        $state.go('warehouseman',$stateParams)
    };

    $scope.goTop = function(){
        $location.hash('topTitle');
        $anchorScroll();
    }
}