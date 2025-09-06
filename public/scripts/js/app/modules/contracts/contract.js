(function(){
angular.module('masDistribucion.contracts',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',ContractConfig])
.controller('ContractsIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','ContractDataService',
            '$http', ContractsIndexController
            ]);
})();