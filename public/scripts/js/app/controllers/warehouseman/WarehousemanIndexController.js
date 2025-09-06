function WarehousemanIndexController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,WarehousemanDataService,UtilsService,CONFIG)
{
    $scope.partials = PARTIALPATH.base;
    $scope.grid = {};
    $scope.selScheduledDate = {};
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                start_date:'desc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = {page:params.page(), rowsPerPage:params.count()};
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});

                WarehousemanDataService.getViewWarehousemanRoutes(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    $defer.resolve(data.data);
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
        $scope.selScheduledDate = schedule;
    }
    
    $scope.go = function(schedule){
        var data = $scope.tableParams.data;
        var $scheduleRouteId = schedule.scheduledRouteId;
        var $routePointId = schedule.routePointId;
        
        if(schedule){
            $state.go('warehouseman.activity',{scheduleRouteId:$scheduleRouteId, routePointId:$routePointId});
        } else {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: '¡Seleccione un registro para poder continuar!'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){});
        }
    }

    $scope.hoverTrIn = function(){
        this.hoverGoSchedule = true;
    }

    $scope.hoverTrOut = function(){
        this.hoverGoSchedule = false;
    }
}
