function TipoMovimientosConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('tipoMovimientos', {
        url: "/tipoMovimientos",
        views:{
            'main':{
                templateUrl: PARTIALPATH.tipoMovimientos + 'index.html',
                controller: 'TipoMovimientosIndexController'
            }
        }
    })
    .state('tipoMovimientos.edit', {
      url:"/{movimientoId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.tipoMovimientos + 'edit.html',
            controller: 'TipoMovimientosEditController',
            resolve: {
                movimiento: ['$stateParams','UtilsService','TipoMovimientosDataService',function($stateParams,UtilsService,TipoMovimientosDataService){                
                var data=TipoMovimientosDataService.getData();
                var movimiento= UtilsService.findById(data,$stateParams.movimientoId)
                if(movimiento) return movimiento
                else{
                    return TipoMovimientosDataService.getTipoMovimientos({id: $stateParams.movimientoId})
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
    .state('tipoMovimientos.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.tipoMovimientos + 'edit.html',
                controller: 'TipoMovimientosEditController',
                resolve: {
                    movimiento: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}