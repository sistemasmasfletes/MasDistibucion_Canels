function ScheduleIndexController($rootScope,$scope,$state,$stateParams,ModalService,ScheduleDataService,PATH,PARTIALPATH,$injector){
 
    ngTableParams = $injector.get('ngTableParams');
    UtilsService = $injector.get('UtilsService');
    $scope.isLoading=false;
    $scope.partials = PARTIALPATH.base;
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
                
                ScheduleDataService.getSchedule(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);
                    
                    $scope.adata = data.data;
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
        this.go(schedule);
    }

    $scope.go = function(schedule){
        if(schedule)
           $state.go('schedule.detail',{routeId:schedule.id});
    }    

    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    

    $scope.customFilter = [ 
        {name:'routeName',type:'text',label:'Ruta'}
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