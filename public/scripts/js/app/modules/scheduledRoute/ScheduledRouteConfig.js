function ScheduledRouteConfig($stateProvider, $urlRouterProvider,$locationProvider, CONFIG){
    $stateProvider
    .state('scheduledRoute', {
        url: "/scheduledRoutes",
        views:{
            'main':{
                templateUrl: CONFIG.PARTIALS + 'scheduledRoute/index.html',
                controller: 'ScheduledRouteIndexController'
            }
        }
    })
    .state('packages', {
        url: "/packages/{orderId:[0-9]{1,6}}",
        views:{
            'main':{
                templateUrl: CONFIG.PARTIALS + 'scheduledRoute/packages.html',
                controller: 'srPackagesController'
            }
        }
    })
    .state('scheduledRoute.schedule',{        
        url:"/route/{scheduleId:[0-9]{1,6}}",        
        views:{
            'schedules':{
                templateUrl: CONFIG.PARTIALS + 'scheduledRoute/schedules.html',
                controller: 'ScheduledRouteSchedulesController',
                resolve: {
                    schedules: ['$stateParams','ScheduledRouteDataService',function($stateParams,ScheduledRouteDataService){                
                        return {};                          
                    }]
                }            
            }
        },
        params:{filter:null}        
    })
    .state('scheduledRoute.schedule.activities',{
        url:"/scheduledRoute/{scheduledRouteId:[0-9]{1,6}}",
        views:{
            'activities@scheduledRoute':{
                templateUrl: CONFIG.PARTIALS + 'scheduledRoute/scheduleActivities.html',
                controller: 'ScheduledRouteActivitiesController'                  
            }
        }
    })
    .state('scheduledRoute.schedule.activities.routePoint',{
        url:"/routePoint/{routePointId:[0-9]{1,6}}",
        views:{
            'routePoint':{
                templateUrl: CONFIG.PARTIALS + 'scheduledRoute/routePointActivities.html',
                controller: 'RoutePointActivitiesController'                  
            }
        },
        params:{scheduledRouteId:null}
    })
}