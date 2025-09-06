function TipoMonedasConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('tipoMonedas', {
        url: "/tipoMonedas",
        views:{
            'main':{
                templateUrl: PARTIALPATH.tipoMonedas + '/index.html',
                controller: 'TipoMonedasIndexController'
            }
        }
    })
    .state('tipoMonedas.edit', {
      url:"/{monedasId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.tipoMonedas + '/edit.html',
            controller: 'TipoMonedasEditController',
            resolve: {
                monedas: ['$stateParams','UtilsService','TipoMonedasDataService',function($stateParams,UtilsService,TipoMonedasDataService){                
                var data=TipoMonedasDataService.getData();
                var monedas= UtilsService.findById(data,$stateParams.monedasId)
                if(monedas) return monedas
                else{
                    return TipoMonedasDataService.getTipoMonedas({id: $stateParams.monedasId})
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
    .state('tipoMonedas.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.tipoMonedas + '/edit.html',
                controller: 'TipoMonedasEditController',
                resolve: {
                    monedas: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}