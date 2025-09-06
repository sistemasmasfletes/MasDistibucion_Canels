(function(){
angular.module('masDistribucion.estatus',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',EstatusConfig])
.controller('EstatusIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','EstatusDataService',
            EstatusIndexController
            ])

.controller('EstatusEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','CatalogService','EstatusDataService',
            'estado','$http',
            EstatusEditController
            ]);
})();