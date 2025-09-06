(function(){
angular.module('masDistribucion.administracionLogCliente',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',AdministracionLogClienteConfig])
.controller('AdministracionLogClienteIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','AdministracionLogClienteDataService', 
            AdministracionLogClienteIndexController
            ])


})();