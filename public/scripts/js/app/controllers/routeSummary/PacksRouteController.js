function PacksRouteController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,ModalService,RouteSummaryDataService,UtilsService,CONFIG){
    
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                shipping_date:'desc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var scheduleRoute= $stateParams.id;
                var postParams = {page:params.page(), rowsPerPage:params.count(),idrow:scheduleRoute};
                
                var filter=params.filter();
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                if(filter) angular.extend(postParams,{filter:filter});

                RouteSummaryDataService.getPacksRoute(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    //params.total(data.meta.totalRecords);
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
    }
    
    $scope.regresar=function(){
        $state.go('routeSummary'); //NAVEGA A PANTALLA DE RUTAS
    }
    
    $scope.toggleFilter = function (params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    $scope.customFilter = [
        {name: 'Paquete', type: 'text', label: 'Paquete'},
        {name: 'ptoVenta', type: 'text', label: 'Punto de Venta'}
    ]

    $scope.filterOpen = false;
    $scope.openFilter = function () {
        $scope.filterOpen = true;
    }

    $scope.appFilter = function (filter) {
        $scope.tableParams.settings().filterDelay = 0;
        $scope.tableParams.$params.filter = filter;
    }
    
    
}