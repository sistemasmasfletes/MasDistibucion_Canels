(function(){
angular.module('masDistribucion.actividaChoferLog',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',ActividaChoferLogConfig])
.controller('ActividaChoferLogIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','ActividaChoferLogDataService',
            ActividaChoferLogIndexController
            ])


})();