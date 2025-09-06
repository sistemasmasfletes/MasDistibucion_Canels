function ScheduledRouteIndexController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,ModalService,ActivityReportDataService,UtilsService,CONFIG){
    
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
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
                var postParams = UtilsService.createNgTablePostParams(params,{groupedByRoute:1});
                                
                ActivityReportDataService.getScheduledRoute(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    params.total(data.meta.totalRecords);
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
        if(schedule)
            $state.go('scheduledRoute.schedule',{scheduleId:schedule.id, fi: $scope.customFilter[0]});
    }

    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    

    $scope.customFilter = [
        {name:'startDate',type:'date',label:'Fecha inicial'},
        {name:'endDate',type:'date',label:'Fecha final'},
        {name:'routeName',type:'text',label:'Ruta'},
        {name:'driverName',type:'text',label:'Conductor'},
        {name:'vehicleName',type:'text',label:'Vehículo'}
    ]

    $scope.filterOpen = false;
    $scope.openFilter = function(){
        $scope.filterOpen = true;  
    }

    $scope.appFilter = function(filter){
        $scope.tableParams.settings().filterDelay=0
        $scope.tableParams.$params.filter=filter;
        
    }
}