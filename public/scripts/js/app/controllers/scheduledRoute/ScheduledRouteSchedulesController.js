function ScheduledRouteSchedulesController($scope,$timeout,$state,$stateParams,ngTableParams,ModalService,ActivityReportDataService,UtilsService,CONFIG,schedules,$injector){
    
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.selScheduledDate = {};
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                scheduled_date:'asc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = UtilsService.createNgTablePostParams(params,{scheduleId:$stateParams.scheduleId,groupedByRoute:0});
                
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
    
    $scope.back=function(){$state.go('^',$stateParams)};
    
    $scope.changeSelection = function(schedule) {
        var data = $scope.tableParams.data;        
        for(var i=0;i<data.length;i++){
            if(data[i].id!=schedule.id)
                data[i].$selected=false;        
        }
        $scope.selScheduledDate = schedule;        
    }

    $scope.go = function(schedule){
        if(schedule)
           $state.go('scheduledRoute.schedule.activities',{scheduledRouteId:schedule.id});       
    }

    $scope.hoverTrIn = function(){
        this.hoverGoSchedule = true;
    }

    $scope.hoverTrOut = function(){
        this.hoverGoSchedule = false;
    }    
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    $scope.customFilter = [
        {name:'startDate',type:'date',label:'Fecha inicial'},
        {name:'endDate',type:'date',label:'Fecha final'},
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
    var parentFilter = $scope.$parent.customFilter;
    var inheritedFilter = {startDate: parentFilter[0]["value"], endDate: parentFilter[1]["value"]};
    $scope.appFilter(inheritedFilter);
}