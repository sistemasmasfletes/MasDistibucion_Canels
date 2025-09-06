(function(){
angular.module('masDistribucion.cuentas',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',CuentasConfig])
.controller('CuentasIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','CuentasDataService',
            CuentasIndexController
            ])

.controller('CuentasEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','CatalogService','CuentasDataService',
            'cuent','$http',
            CuentasEditController
            ]);
})();