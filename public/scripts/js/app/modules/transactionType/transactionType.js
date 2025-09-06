(function(){
angular.module('masDistribucion.transactionType',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',TransactionTypeConfig])
.controller('TransactionTypeIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','TransactionTypeDataService',
            TransactionTypeIndexController
            ])

.controller('TransactionTypeEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PARTIALPATH','ModalService','CatalogService','TransactionTypeDataService',
            'transaction',
            TransactionTypeEditController
            ]);
})();