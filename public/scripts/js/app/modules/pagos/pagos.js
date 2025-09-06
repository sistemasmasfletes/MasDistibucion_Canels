(function(){
angular.module('masDistribucion.pagos',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',PagosConfig])
.controller('PagosIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','PagosDataService',
            PagosIndexController
            ])


})();