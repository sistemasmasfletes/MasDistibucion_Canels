(function(){
angular.module('masDistribucion.actividadesChofer',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',ActividadesChoferConfig])
.controller('ActividadesChoferIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','ActividadesChoferDataService',
            ActividadesChoferIndexController
            ])


})();