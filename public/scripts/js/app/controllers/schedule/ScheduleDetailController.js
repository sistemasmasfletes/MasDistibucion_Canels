function ScheduleDetailController($rootScope,$scope,$state,$stateParams,ModalService,ScheduleDataService,PARTIALPATH,$injector){
  
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    ngTableParams = $injector.get('ngTableParams');
    UtilsService = $injector.get('UtilsService');
    timeout =  $injector.get('$timeout');

    $scope.isLoading=false;
    $scope.partials = PARTIALPATH.base;

    $scope.grid={};
    $scope.grid.delete = function(){grdDelete($scope.scheduleId)};
    $scope.grid.add = grdAdd;
    $scope.grid.edit = function(){return grdEdit($scope.scheduleId)};

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
                var postParams = UtilsService.createNgTablePostParams(params,{routeId:$stateParams.routeId});
               
                ScheduleDataService.getScheduleDetail(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);

                    $scope.scheduleId = null;
                    $scope.updateParentGrid();
                    $scope.updateGridState(data.data);
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
        $scope.scheduleId = schedule.id;
    }

    $scope.go = function(schedule){
        $scope.grid.edit();
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

    $scope.getDateFromString = function(dateString){        
        return UtilsService.getDateFromString(dateString);
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

    function grdAdd(){       
        $state.go('schedule.detail.add',$stateParams);
    }

    $scope.grdEdit = function(id){
        if(id)
           $state.go('schedule.detail.edit',{scheduleId:id}); 
        else{
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder editar, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        }
    }

    $scope.goScheduledDates=function(id){        
        if(id)
           $state.go('schedule.detail.scheduledDates',{scheduleId:id}); 
        else{
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Seleccione primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        } 
    }

    $scope.grdDelete = function(id){
        if (id) {
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar esta Programación?'
            };
            ModalService.showModal({templateUrl:modalPath}, modalOptions).then(function (result) {
                $scope.loading = true;
                config.alertOnSuccess=true;
                ScheduleDataService.delete({id: id},config)
                .success(function(data, status, headers, config) {
                    $scope.loading = false;
                    if(!data.error){
                        $scope.tableParams.reload();
                    }                 
                })
                .error(function(data, status, headers, config) {
                    $scope.loading = false;
                });
            });
        }else{
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder eliminar, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        }
    }
    
    $scope.updateParentGrid = function(){       
        if($stateParams.routeId){
            parentData = $scope.$parent.tableParams.data;           
            for(var i=0;i<parentData.length;i++)
                if(parentData[i].id==$stateParams.routeId)
                   parentData[i].$selected=true; 
        }       
    }

    $scope.updateGridState=function(data){
         if($stateParams.selectedId && $stateParams.selectedId>0){              
            for(var i=0;i<data.length;i++)
                if(data[i].id==$stateParams.selectedId){
                   data[i].$selected=true;
                   $scope.scheduleId = data[i].id;
                }
        }
    }
}