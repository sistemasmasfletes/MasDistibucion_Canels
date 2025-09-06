function ScheduledDatesEditController($scope,$timeout,$state,$stateParams,$filter,PARTIALPATH,ModalService,CatalogService,ScheduleDataService,RoutesDataService,VehiclesDataService,UsersDataService,UtilsService,schedule){
    $scope.schedule = schedule;
    $scope.sc = {loading:false}
    $scope.back = function(){
        $state.go('^',$stateParams);
    }

    var selectOptions = {
        displayText: "Seleccione...",
        emptyListText:"No hay elementos a desplegar",
        emptySearchResultText:"No se encontraron resultados para '$0'",
        searchDelay:"500"
    }

    $scope.selRouteOptions = angular.extend({},selectOptions
        ,{onSelect:function($item){
            $scope.schedule.routeid = ($item) ? $item.id : null;
        }});
    
    $scope.selVehicleOptions = angular.extend({},selectOptions
        ,{onSelect:function($item){
            $scope.schedule.vehicleid = ($item) ? $item.id : null;
        }});

    $scope.selDriverOptions = angular.extend({},selectOptions
        ,{onSelect:function($item){
            $scope.schedule.driverid = ($item)? $item.id : null;
        }});     
   

    $scope.getRoutes=function(value){
        if(value.length==0) return;
        return RoutesDataService.getRoutesByName({param1:value})
        .then(function(response){
            var arrData = response.data[0];
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

    $scope.selRoute={id:$scope.schedule.routeid, route:$scope.schedule.route}
    $scope.selVehicle={id:$scope.schedule.vehicleid,name:$scope.schedule.vehicle}
    $scope.selDriver={id:$scope.schedule.driverid,driver:$scope.schedule.driver}

    $scope.getFormFieldCssClass = function(ngModelController) {
        if(ngModelController.$pristine) return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    }
    $scope.schedule.date = UtilsService.getDateFromString($scope.schedule.scheduled_date);
    $scope.schedule.datestring = $filter('date')($scope.schedule.date, 'dd-MM-yyyy HH:mm');

    $scope.save = function(){
        if($scope.schedule){
            $scope.sc.loading=true;           
 
            config.alertOnSuccess=true;
            ScheduleDataService.updateScheduledDate($scope.schedule, config)
                .success(function(data, status, headers, config){
                    $scope.sc.loading=false;
                    if (!data.error) {                        
                        $scope.$$prevSibling.tableParams.reload().then(function(){
                            $scope.back();
                        });                        
                    }                    
                })
                .error(function(data, status, headers, config){
                    $scope.sc.loading = false;                   
                });
        }
    }
}