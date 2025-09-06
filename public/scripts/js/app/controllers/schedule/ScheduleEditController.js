function ScheduleEditController($scope,$timeout,$state,$stateParams,$filter,PARTIALPATH,ModalService,CatalogService,ScheduleDataService,RoutesDataService,VehiclesDataService,UsersDataService,UtilsService,schedule){
    $scope.schedule=schedule;
    $scope.sc = {loading:false}
    var selectOptions = {
        displayText: "Seleccione...",
        emptyListText:"No hay elementos a desplegar",
        emptySearchResultText:"No se encontraron resultados para '$0'",
        searchDelay:"500"
    }

    $scope.back=function(){        
        $stateParams.selectedId=$stateParams.scheduleId;
        $stateParams.triggerReload = $stateParams.triggerReload === null ? "" : null;
        $state.go('^',$stateParams/*,{ reload: false, inherit: true, notify: true }*/)
    };
    $scope.save=save;
    
    $scope.sc.dt = new Date();
    if($scope.schedule.start_date){
        $scope.sc.dt = UtilsService.getDateFromString($scope.schedule.start_date);
        $scope.sc.dtEnd = UtilsService.getDateFromString($scope.schedule.end_date);
    }  

    $scope.selRouteOptions = angular.extend({},selectOptions
        ,{onSelect:function($item){
            $scope.schedule.route_id = ($item) ? $item.id : null;
        }});
    
    $scope.selVehicleOptions = angular.extend({},selectOptions
        ,{onSelect:function($item){
            $scope.schedule.vehicle_id = ($item) ? $item.id : null;
        }});

    $scope.selDriverOptions = angular.extend({},selectOptions
        ,{onSelect:function($item){
            $scope.schedule.user_id = ($item)? $item.id : null;
        }});     
   

    $scope.getRoutes=function(value){
        if(value.length==0) return;
        return RoutesDataService.getRoutesByName({param1:value})
        .then(function(response){
            var arrData = response.data[0];
            //arrData.unshift({id:null,route:'-- Sin selección --'});
            return arrData;
        });
    }

    $scope.getVehicles=function(value){
        if(value.length==0) return;
        return VehiclesDataService.getVehiclesByName({param1:value})
        .then(function(response){
            return response.data[0];
        });
    }

    $scope.getDrivers=function(value){
        if(value.length==0) return;
        return UsersDataService.getDriverbyName({param1:value})
        .then(function(response){
            return response.data[0];
        });
    }

    $scope.selRoute={id:$scope.schedule.route_id, route:$scope.schedule.route}
    $scope.selVehicle={id:$scope.schedule.vehicle_id,name:$scope.schedule.vehicle}
    $scope.selDriver={id:$scope.schedule.user_id,driver:$scope.schedule.driver}

    $scope.schedule.status+='';
    $scope.schedule.week+='';
    $scope.schedule.recurrent+='';
    $scope.schedule.sunday+='';
    $scope.schedule.monday+='';
    $scope.schedule.tuesday+='';
    $scope.schedule.wednesday+='';
    $scope.schedule.thursday+='';
    $scope.schedule.friday+='';
    $scope.schedule.saturday+='';

    /* DatePicker*/
    $scope.datePicker = {
        format: 'dd-MM-yyyy',
        toggleMin: function(){
            $scope.datePicker.minDate = null//$scope.datePicker.minDate ? null : new Date();
        },
        open: function($event){
            $event.preventDefault();
            $event.stopPropagation();
            $scope.datePicker.opened = true;
        },
        dateOptions : {
            formatYear: 'yy',
            startingDay: 1        
        }
    }  
    $scope.datePicker.toggleMin();

    $scope.datePicker2 = {
        format: 'dd-MM-yyyy',
        toggleMin: function(){
            $scope.datePicker2.minDate = null;
        },
        open: function($event){
            $event.preventDefault();
            $event.stopPropagation();
            $scope.datePicker2.opened = true;
        },
        dateOptions : {
            formatYear: 'yy',
            startingDay: 1        
        }
    } 

    function save(){
        if($scope.schedule){
            $scope.sc.loading=true;
            $scope.schedule.start_date = $filter('date')($scope.sc.dt, 'yyyy-MM-dd HH:mm:ss');
            $scope.schedule.end_date = $filter('date')($scope.sc.dtEnd, 'yyyy-MM-dd HH:mm:ss');
            
            if($scope.schedule.scheduleParent_id=='') $scope.schedule.scheduleParent_id=null;            

            config.alertOnSuccess=true;
            ScheduleDataService.save($scope.schedule, config)
                .success(function(data, status, headers, config){
                    $scope.sc.loading=false;
                    if (!data.error) {
                        $scope.back();                        
                    }                    
                })
                .error(function(data, status, headers, config){
                    $scope.sc.loading = false;                   
                });
        }
    }

    $scope.getFormFieldCssClass = function(ngModelController) {
        if(ngModelController.$pristine) return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    }

    $scope.toggleWeekDays=function(){
        $scope.schedule.week=$scope.schedule.recurrent;
        if($scope.schedule.recurrent==0){
            $scope.schedule.sunday=0;
            $scope.schedule.monday=0;
            $scope.schedule.tuesday=0;
            $scope.schedule.wednesday=0;
            $scope.schedule.thursday=0;
            $scope.schedule.friday=0;
            $scope.schedule.saturday=0;

            $scope.sc.dtEnd = null;
        }else{
            $scope.sc.dtEnd = new Date();
        }
    }    
}