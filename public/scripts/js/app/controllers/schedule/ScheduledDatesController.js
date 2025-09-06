function ScheduledDatesController($scope,$state,$stateParams,ModalService,ScheduleDataService,PARTIALPATH,$injector){
    ngTableParams = $injector.get('ngTableParams');
    UtilsService = $injector.get('UtilsService');
    $scope.isLoading=false;
    $scope.partials = PARTIALPATH.base;
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                scheduled_date:'desc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = UtilsService.createNgTablePostParams(params,{scheduleId:$stateParams.scheduleId});
                
                ScheduleDataService.getScheduledDates(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);
                    
                    $scope.adata = data.data;
                    ScheduleDataService.setLocalScheduledDates(data.data);
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
        if(schedule)
           $state.go('schedule.detail',{routeId:schedule.id});
    }    

    $scope.goEdit = function(schedule){
        if(schedule)
           $state.go('schedule.detail.scheduledDates.edit',{schedDatesId:schedule.id});
    }

    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    $scope.back=function(){$state.go('^',$stateParams)};

    $scope.customFilter = [ 
        {name:'scheduled_date',type:'date',label:'Fecha programada'}
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