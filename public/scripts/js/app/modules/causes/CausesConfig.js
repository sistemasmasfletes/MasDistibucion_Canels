function CausesConfig($stateProvider, $urlRouterProvider, $locationProvider, PARTIALPATH){
    
    $stateProvider
    .state('causes', {
        url: "/causes",
        views:{
            'main':{
                templateUrl: PARTIALPATH.causes + 'index.html',
                controller: 'CausesIndexController'
            }
        }
    })
    .state('causes.edit', {
      url:"/{causeId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.causes + 'edit.html',
            controller: 'CausesEditController',
            resolve: {
                cause: ['$stateParams','UtilsService','CausesDataService',function($stateParams,UtilsService,CausesDataService){
                var data=CausesDataService.getData();
                var cause = UtilsService.findById(data,$stateParams.causeId)
                if(cause) return cause
                else{
                    return CausesDataService.getCauses({id: $stateParams.causeId})
                        .then(function(response){
                            console.log('resolve');
                            console.log($stateParams)
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
    .state('causes.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.causes + 'edit.html',
                controller: 'CausesEditController',
                resolve: {
                    cause: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}