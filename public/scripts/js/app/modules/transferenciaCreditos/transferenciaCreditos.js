(function(){
angular.module('masDistribucion.transferenciaCreditos',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',TransferenciaCreditosConfig])
.controller('TransferenciaCreditosIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','TransferenciaCreditosDataService',
            TransferenciaCreditosIndexController
            ])

.controller('TransferenciaCreditosEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','CatalogService','TransferenciaCreditosDataService', '$http',
            'transferencia', 'UsersDataService',
            TransferenciaCreditosEditController
            ]);
})();