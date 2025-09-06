function VehiclesConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('vehicles', {
        url: "/vehicles",
        views:{
            'main':{
                templateUrl: PARTIALPATH.vehicles + 'index.html',
                controller: 'VehiclesIndexController'
            }
        }
    })
    .state('vehicles.edit', {
      url:"/{vehicleId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.vehicles + 'edit.html',
            controller: 'VehiclesEditController',
            resolve: {
                vehicle: ['$stateParams','UtilsService','VehiclesDataService',function($stateParams,UtilsService,VehiclesDataService){                
                var data=VehiclesDataService.getData();
                var vehicle = UtilsService.findById(data,$stateParams.vehicleId)
                if(vehicle) return vehicle
                else{
                    return VehiclesDataService.getVehicles({id: $stateParams.vehicleId})
                        .then(function(response){
                            if(response && angular.isArray(response.data) && response.data.length>0 && response.data[0].length>0)
                                return response.data[0][0];
                            else
                                return {};
                        })
                }
                
            }]
            }
        }
      }          
    })
    .state('vehicles.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.vehicles + 'edit.html',
                controller: 'VehiclesEditController',
                resolve: {
                    vehicle: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}