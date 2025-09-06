(function(){
angular.module('masDistribucion.tipoMovimientos',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',TipoMovimientosConfig])
.controller('TipoMovimientosIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','TipoMovimientosDataService',
            TipoMovimientosIndexController
            ])

.controller('TipoMovimientosEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','CatalogService','TipoMovimientosDataService',
            'movimiento','$http',
            TipoMovimientosEditController
            ]);
})();