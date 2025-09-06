function TipoPagosConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('tipoPagos', {
        url: "/tipoPagos",
        views:{
            'main':{
                templateUrl: PARTIALPATH.tipoPagos + 'index.html',
                controller: 'TipoPagosIndexController'
            }
        }
    })
    .state('tipoPagos.edit', {
      url:"/{tipoId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.tipoPagos + 'edit.html',
            controller: 'TipoPagosEditController',
            resolve: {
                tipo: ['$stateParams','UtilsService','TipoPagosDataService',function($stateParams,UtilsService,TipoPagosDataService){                
                var data=TipoPagosDataService.getData();
                var tipo = UtilsService.findById(data,$stateParams.tipoId)
                if(tipo) return tipo
                else{
                    return TipoPagosDataService.getTipoPagos({id: $stateParams.tipoId})
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
    .state('tipoPagos.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.tipoPagos + 'edit.html',
                controller: 'TipoPagosEditController',
                resolve: {
                    tipo: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}