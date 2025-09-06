function ScheduledRouteActivitiesController($scope,$location,$anchorScroll,$timeout,$state,$stateParams,ngTableParams,ModalService,ActivityReportDataService,UtilsService,CONFIG){
    
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.scheduledDate = $scope.$parent.selScheduledDate;
    $scope.currentData = [];
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:50,
            sorting:{
                id:'asc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = UtilsService.createNgTablePostParams(params,{scheduledRouteId:$stateParams.scheduledRouteId});
                
                ActivityReportDataService.getScheduledRouteActivityDetail(postParams)
                .then(function(response){
                    $scope.currentData=response.data;
                    $scope.isLoading=false;
                    params.total($scope.currentData.meta.totalRecords);
                    $defer.resolve($scope.currentData.data[0]);
                   
                    $scope.routeDate = UtilsService.getDateFromString($scope.currentData.data[1][0]["scheduled_date"]);//$scope.getDateFromString($scope.currentData.data[1][0]["scheduled_date"]);
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
        if(schedule){
            $state.go('scheduledRoute.schedule.activities.routePoint',{scheduledRouteId:$stateParams.scheduledRouteId, routePointId:schedule.id});
            $scope.goTop();
        }
    }

    $scope.getDateFromString = function(dateString){        
        return UtilsService.getDateFromString(dateString);
    }   
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    
    $scope.back=function(){$state.go('scheduledRoute.schedule',$stateParams)};

    $scope.goTop = function(){
        $location.hash('topTitle');
        $anchorScroll();
    }

    $scope.customFilter = [
        {name:'iniDate',type:'date',label:'Fecha inicial'},
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
