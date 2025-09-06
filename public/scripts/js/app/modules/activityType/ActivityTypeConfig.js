function ActivityTypeConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('activityType', {
        url: "/activityType",
        views:{
            'main':{
                templateUrl: PARTIALPATH.activityType + 'index.html',
                controller: 'ActivityTypeIndexController'
            }
        }
    })
    .state('activityType.edit', {
      url:"/{activityId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.activityType + 'edit.html',
            controller: 'ActivityTypeEditController',
            resolve: {
                activity: ['$stateParams','UtilsService','ActivityTypeDataService',function($stateParams,UtilsService,ActivityTypeDataService){                
                var data=ActivityTypeDataService.getData();
                var activity = UtilsService.findById(data,$stateParams.activityId)
                if(activity) return activity
                else{
                    return ActivityTypeDataService.getActivityType({id: $stateParams.activityId})
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
    .state('activityType.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.activityType + 'edit.html',
                controller: 'ActivityTypeEditController',
                resolve: {
                    activity: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}