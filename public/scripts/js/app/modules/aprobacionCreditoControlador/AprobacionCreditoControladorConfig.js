function AprobacionCreditoControladorConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('aprobacionCreditoControlador', {
        url: "/aprobacionCreditoControlador",
        views:{
            'main':{
                templateUrl: PARTIALPATH.aprobacionCreditoControlador + 'index.html',
                controller: 'AprobacionCreditoControladorIndexController'
            }
        }
    })
    .state('aprobacionCreditoControlador.edit', {
      url:"/{compraId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.aprobacionCreditoControlador + 'edit.html',
            controller: 'AprobacionCreditoControladorEditController',
            resolve: {
                aprobacion: ['$stateParams','UtilsService','AprobacionCreditoControladorDataService',function($stateParams,UtilsService,AprobacionCreditoControladorDataService){                
                var data=AprobacionCreditoControladorDataService.getData();
                var aprobacion= UtilsService.findById(data,$stateParams.aprobacionId)
                if(aprobacion) return aprobacion;
                else{
                    return AprobacionCreditoControladorDataService.getAprobacionCreditoControlador({id: $stateParams.compraId})
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
    .state('aprobacionCreditoControlador.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.aprobacionCreditoControlador + 'edit.html',
                controller: 'aprobacionCreditoControladorEditController',
                resolve: {
                    aprobacion: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}