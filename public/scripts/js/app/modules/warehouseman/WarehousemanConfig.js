function WarehousemanConfig ($stateProvider, $urlRouterProvider, $locationProvider, CONFIG /*PARTIALPATH*/){
    
    $stateProvider
    .state('warehouseman', {
        url: "/warehouseman",
        views:{
            'main':{
                templateUrl: CONFIG.PARTIALS + 'warehouseman/index.html',
                controller: 'WarehousemanIndexController'
            }
        }
    })
    
    /*.state('warehouseman.point',{
        url:"/route/{routeId:[0-9]{1,6}}",
        views:{
            'edit':{
                templateUrl: CONFIG.PARTIALS + 'warehouseman/edit.html',
                controller: 'WarehousemanPointController'
                 
            }
        }
    })
    
    .state('warehouseman.pack',{
        url:"/route/{routeId:[0-9]{1,6}}/scheduleRoute/{scheduleRouteId:[0-9]{1,6}}",
        //url:"/scheduleRoute/{scheduleRouteId:[0-9]{1,6}}",
        views:{
            'edit':{
                templateUrl: CONFIG.PARTIALS + 'warehouseman/edit2.html',
                controller: 'WarehousemanPackController' 
            }
        }
    })*/
    
    .state('warehouseman.activity',{
        //url:"/route/{routeId:[0-9]{1,6}}/scheduleRoute/{scheduleRouteId:[0-9]{1,6}}/routePoint/{routePointId:[0-9]{1,6}}",
        url:"/scheduleRoute/{scheduleRouteId:[0-9]{1,6}}/routePoint/{routePointId:[0-9]{1,6}}",
        views:{
            'edit':{
                templateUrl: CONFIG.PARTIALS + 'warehouseman/activity.html',
                controller: 'WarehousemanActivityController'
            }
        }
    })
    
    .state('warehouseman.form', {
      url:"/scheduleRoute/{scheduleRouteId:[0-9]{1,8}}/routePoint/{routePointId:[0-9]{1,8}}/routePointActivity/{routePointActivityId:[0-9]{1,8}}",
      views:{
        'edit': {
            templateUrl: CONFIG.PARTIALS + 'warehouseman/form.html',
            controller: 'WarehousemanSaveTransferController',
            resolve: {
                warehouseman: ['$stateParams','UtilsService','WarehousemanDataService',function($stateParams,UtilsService,WarehousemanDataService){                
                var data=WarehousemanDataService.getData();
                var warehouseman = UtilsService.findById(data,$stateParams)
                if(warehouseman) return warehouseman
                else{
                    return WarehousemanDataService.getWarehousemanActivity({id: $stateParams.routePointActivityId})
                        .then(function(response){
                            if(response.data && response.data.data.length>0)
                                return response.data.data[0];
                            else
                                return {};
                        })
                }
                
            }]
            }
        }
      }          
    })
    
    .state('warehouseman.tracking', {
        url: "/packages/{orderId:[0-9]{1,6}}",
        views:{
            'edit':{
                templateUrl: CONFIG.PARTIALS + 'warehouseman/tracking.html',
                controller: 'WarehousemanPackageTrackingController'
            }
        }
    })
    
    /************************************PAQUETES RECHAZADOS ************************************************/
    .state('warehouseman.rejected', {
        url: "/packages/{orderId:[0-9]{1,6}}",
        views:{
            'edit':{
                templateUrl: CONFIG.PARTIALS + 'warehouseman/rejected.html',
                controller: 'WarehousemanRejectedController'
            }
        }
    })
    
    .state('warehouseman.edit', {
        url: "/{Oid:[0-9]{1,6}}",
        views:{
            'edit':{
                templateUrl: CONFIG.PARTIALS + 'warehouseman/rejectededit.html',
                controller: 'WarehousemanRejectededitController',
                resolve: {
                    orderId: function($stateParams){
                    	var orderId = $stateParams
                        return orderId                                  
                    }
                }
            }
        }
    });
    /************************************PAQUETES RECHAZADOS ************************************************/
    
}
