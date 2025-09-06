(function(){
angular.module('masDistribucion.actividadChoferLog',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',ActividadChoferLogConfig])
.controller('ActividadChoferLogIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','ActividadChoferLogDataService',
            ActividadChoferLogIndexController
            ])


})();