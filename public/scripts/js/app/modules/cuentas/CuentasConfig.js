function CuentasConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('cuentas', {
        url: "/cuentas",
        views:{
            'main':{
                templateUrl: PARTIALPATH.cuentas + '/index.html',
                controller: 'CuentasIndexController'
            }
        }
    })
    .state('cuentas.edit', {
      url:"/{cuentId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.cuentas + '/edit.html',
            controller: 'CuentasEditController',
            resolve: {
                cuent: ['$stateParams','UtilsService','CuentasDataService',function($stateParams,UtilsService,CuentasDataService){                
                var data=CuentasDataService.getData();
                var cuent= UtilsService.findById(data,$stateParams.cuentId)
                if(cuent) return cuent
                else{
                    return CuentasDataService.getCuentas({id: $stateParams.cuentId})
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
    .state('cuentas.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.cuentas + '/edit.html',
                controller: 'CuentasEditController',
                resolve: {
                    cuent: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}