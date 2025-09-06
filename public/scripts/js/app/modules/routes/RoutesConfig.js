function RoutesConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
	$stateProvider
	.state('routes', {
		url: "/routes",
		views:{
			'main':{
			    templateUrl: PARTIALPATH.routes + 'index.html',
			    controller: 'RoutesIndexController',
			    resolve: {
			        routes:['RoutesDataService',function(RoutesDataService){return {}}]
			    }
			}
    	}
    })
    .state('routes.edit', {
      url:"/{routeId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.routes + 'edit.html',
            controller: 'RoutesEditController',
            resolve: {
                route: ['$stateParams','RoutesDataService',function($stateParams,RoutesDataService){
                var data=RoutesDataService.getData();
                var route = RoutesDataService.findById(data,$stateParams.routeId)
                if(route) return route
                else{
                    return RoutesDataService.getRoutes({id: $stateParams.routeId})
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
    .state('routes.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.routes + 'edit.html',
                controller: 'RoutesEditController',
                resolve: {
                    route: function(){
                        return {};                                  
                    }
                }
            }
        }          
    })
    .state('routes.edit.points', {
        url:"/points",
        views:{
            '': {
                templateUrl: PARTIALPATH.routes + 'edit.points.html',
                controller: 'RoutesEditPointsController',
                resolve: {
                    points: ['$stateParams','PointsDataService',function($stateParams,PointsDataService){
                        return {};
                    }]
                }
            }
        }          
    });
}