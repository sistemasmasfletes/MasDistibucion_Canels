function TransferenciaCreditosConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('transferenciaCreditos', {
        url: "/transferenciaCreditos",
        views:{
            'main':{
                templateUrl: PARTIALPATH.transferenciaCreditos + 'index.html',
                controller: 'TransferenciaCreditosIndexController'
            }
        }
    })
    .state('transferenciaCreditos.edit', {
      url:"/{transferenciaId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.transferenciaCreditos + 'edit.html',
            controller: 'TransferenciaCreditosEditController',
            resolve: {
                transferencia: ['$stateParams','UtilsService','TransferenciaCreditosDataService',function($stateParams,UtilsService,TransferenciaCreditosDataService){                
                var data=TransferenciaCreditosDataService.getData();
                var transferencia= UtilsService.findById(data,$stateParams.transferenciaId)
                if(transferencia) return transferencia
                else{
                    return TransferenciaCreditosDataService.getTransferenciaCreditos({id: $stateParams.transferenciaId})
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
    .state('transferenciaCreditos.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.transferenciaCreditos + 'edit.html',
                controller: 'TransferenciaCreditosEditController',
                resolve: {
                    transferencia: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}