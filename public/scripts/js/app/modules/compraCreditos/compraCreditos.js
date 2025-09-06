(function(){
angular.module('masDistribucion.compraCreditos',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',CompraCreditosConfig])
.controller('CompraCreditosIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','CompraCreditosDataService',
            '$http', CompraCreditosIndexController
            ])

.controller('CompraCreditosEditController',
            ['$scope','$timeout','$state','$stateParams',
            'CONFIG','PATH','PARTIALPATH','ModalService','CatalogService','CompraCreditosDataService',
            'compra', '$http',
            CompraCreditosEditController
            ]);
})();