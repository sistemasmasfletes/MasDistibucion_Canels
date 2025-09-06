function ActividaChoferLogConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('actividaChoferLog', {
        url: "/actividaChoferLog",
        views:{
            'main':{
                templateUrl: PARTIALPATH.actividaChoferLog + 'index.html',
                controller: 'ActividaChoferLogIndexController'
            }
        }
    })
    .state('actividaChoferLog.edit', {
      url:"/{actividaChoferLogId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.actividaChoferLog + 'edit.html',
            controller: 'ActividaChoferLogEditController',
            resolve: {
                actividaChoferLog: ['$stateParams','UtilsService','ActividaChoferLogDataService',function($stateParams,UtilsService,ActividaChoferLogDataService){                
                var data=ActividaChoferLogDataService.getData();
                var actividaChoferLog= UtilsService.findById(data,$stateParams.actividaChoferLogId);
                if(actividaChoferLog) return actividaChoferLog;
                else{
                    return ActividaChoferLogDataService.getActividaChoferLog({id: $stateParams.actividaChoferLogId})
                        .then(function(response){
                            if(response && angular.isArray(response.data) && response.data.length>0 && response.data[0].length>0)
                                return response.data[0][0];
                            else
                                return {};
                        });
                }
                
            }]
            }
        }
      }          
    })
    
}