function CompraCreditosConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('compraCreditos', {
        url: "/compraCreditos",
        views:{
            'main':{
                templateUrl: PARTIALPATH.compraCreditos + 'index.html',
                controller: 'CompraCreditosIndexController'
            }
        }
    })
    .state('compraCreditos.edit', {
      url:"/{compraId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.compraCreditos + 'edit.html',
            controller: 'CompraCreditosEditController',
            resolve: {
                compra: ['$stateParams','UtilsService','CompraCreditosDataService',function($stateParams,UtilsService,CompraCreditosDataService){                
                var data=CompraCreditosDataService.getData();
                var compra= UtilsService.findById(data,$stateParams.compraId);
                if(compra) return compra;
                else{
                    return CompraCreditosDataService.getCompraCreditos({id: $stateParams.compraId})
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
    .state('compraCreditos.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.compraCreditos + 'edit.html',
                controller: 'CompraCreditosEditController',
                resolve: {
                    compra: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
    
}