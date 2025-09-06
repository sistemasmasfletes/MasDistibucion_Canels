(function(){
angular.module('masDistribucion.aprobacionCreditoControlador',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',AprobacionCreditoControladorConfig])
.controller('AprobacionCreditoControladorIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','AprobacionCreditoControladorDataService',
            AprobacionCreditoControladorIndexController
            ])

.controller('AprobacionCreditoControladorEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PARTIALPATH','ModalService','CatalogService','AprobacionCreditoControladorDataService',
            'aprobacionCredito',
            AprobacionCreditoControladorEditController
            ]);
})();