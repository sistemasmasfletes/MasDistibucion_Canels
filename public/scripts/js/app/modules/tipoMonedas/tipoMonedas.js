(function(){
angular.module('masDistribucion.tipoMonedas',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',TipoMonedasConfig])
.controller('TipoMonedasIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','TipoMonedasDataService',
            TipoMonedasIndexController
            ])

.controller('TipoMonedasEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','CatalogService','TipoMonedasDataService',
            'monedas','$http',
            TipoMonedasEditController
            ]);
})();