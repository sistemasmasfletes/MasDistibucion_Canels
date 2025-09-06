function AprobacionCreditosConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('aprobacionCreditos', {
        url: "/aprobacionCreditos",
        views:{
            'main':{
                templateUrl: PARTIALPATH.aprobacionCreditos + 'index.html',
                controller: 'AprobacionCreditosIndexController'
            }
        }
    })
    .state('aprobacionCreditos.edit', {
      url:"/{aprobacionId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.aprobacionCreditos + 'edit.html',
            controller: 'AprobacionCreditosEditController',
            resolve: {
                aprobacion: ['$stateParams','UtilsService','AprobacionCreditosDataService',function($stateParams,UtilsService,AprobacionCreditosDataService){                
                var data=AprobacionCreditosDataService.getData();
                var aprobacion= UtilsService.findById(data,$stateParams.aprobacionId);
                if(aprobacion) return aprobacion;
                else{
                    return AprobacionCreditosDataService.getAprobacionCreditos({id: $stateParams.aprobacionId})
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
    .state('aprobacionCreditos.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.aprobacionCreditos + 'edit.html',
                controller: 'AprobacionCreditosEditController',
                resolve: {
                    aprobacion: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}