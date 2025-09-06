(function(){
angular.module('masDistribucion.aprobacionCreditos',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',AprobacionCreditosConfig])
.controller('AprobacionCreditosIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','AprobacionCreditosDataService',
            AprobacionCreditosIndexController
            ])

.controller('AprobacionCreditosEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PARTIALPATH','ModalService','CatalogService','AprobacionCreditosDataService',
            'aprobacion',
            AprobacionCreditosEditController
            ]);
})();