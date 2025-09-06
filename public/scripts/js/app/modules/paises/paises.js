(function(){
angular.module('masDistribucion.paises',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',PaisesConfig])
.controller('PaisesIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','PaisesDataService',
            PaisesIndexController
            ])

.controller('PaisesEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','CatalogService','PaisesDataService',
            'pais','$http',
            PaisesEditController
            ]);
})();