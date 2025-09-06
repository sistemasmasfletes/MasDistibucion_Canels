(function(){
angular.module('masDistribucion.balanceGeneral',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',BalanceGeneralConfig])
.controller('BalanceGeneralIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','BalanceGeneralDataService',
            BalanceGeneralIndexController
            ])


})();