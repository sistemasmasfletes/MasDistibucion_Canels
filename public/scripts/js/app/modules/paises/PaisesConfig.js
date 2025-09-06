function PaisesConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('paises', {
        url: "/paises",
        views:{
            'main':{
                templateUrl: PARTIALPATH.paises + '/index.html',
                controller: 'PaisesIndexController'
            }
        }
    })
    .state('paises.edit', {
      url:"/{paisId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.paises + '/edit.html',
            controller: 'PaisesEditController',
            resolve: {
                pais: ['$stateParams','UtilsService','PaisesDataService',function($stateParams,UtilsService,PaisesDataService){                
                var data=PaisesDataService.getData();
                var pais= UtilsService.findById(data,$stateParams.paisId)
                if(pais) return pais
                else{
                    return PaisesDataService.getPaises({id: $stateParams.paisId})
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
    .state('paises.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.paises + '/edit.html',
                controller: 'PaisesEditController',
                resolve: {
                    pais: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}