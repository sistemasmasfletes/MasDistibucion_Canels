function BancosConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('bancos', {
        url: "/bancos",
        views:{
            'main':{
                templateUrl: PARTIALPATH.bancos + 'index.html',
                controller: 'BancosIndexController'
            }
        }
    })
    .state('bancos.edit', {
      url:"/{bancoId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.bancos + 'edit.html',
            controller: 'BancosEditController',
            resolve: {
                banco: ['$stateParams','UtilsService','BancosDataService',function($stateParams,UtilsService,BancosDataService){                
                var data=BancosDataService.getData();
                var banco = UtilsService.findById(data,$stateParams.bancoId)
                if(banco) return banco
                else{
                    return BancosDataService.getBancos({id: $stateParams.bancoId})
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
    .state('bancos.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.bancos + 'edit.html',
                controller: 'BancosEditController',
                resolve: {
                    banco: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}