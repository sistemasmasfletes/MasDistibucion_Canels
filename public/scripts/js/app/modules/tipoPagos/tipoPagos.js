(function(){
angular.module('masDistribucion.tipoPagos',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',TipoPagosConfig])
.controller('TipoPagosIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','TipoPagosDataService',
            TipoPagosIndexController
            ])

.controller('TipoPagosEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','CatalogService','TipoPagosDataService',
            'tipo', '$http',
            TipoPagosEditController
            ]);
})();