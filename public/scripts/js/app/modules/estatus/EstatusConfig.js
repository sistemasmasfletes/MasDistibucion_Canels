function EstatusConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('estatus', {
        url: "/estatus",
        views:{
            'main':{
                templateUrl: PARTIALPATH.estatus + 'index.html',
                controller: 'EstatusIndexController'
            }
        }
    })
    .state('estatus.edit', {
      url:"/{estatuId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.estatus + 'edit.html',
            controller: 'EstatusEditController',
            resolve: {
                estado: ['$stateParams','UtilsService','EstatusDataService',function($stateParams,UtilsService,EstatusDataService){                
                var data=EstatusDataService.getData();
                var estado= UtilsService.findById(data,$stateParams.estatuId)
                if(estado) return estado
                else{
                    return EstatusDataService.getEstatus({id: $stateParams.estatuId})
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
    .state('estatus.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.estatus + 'edit.html',
                controller: 'EstatusEditController',
                resolve: {
                   estado: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}