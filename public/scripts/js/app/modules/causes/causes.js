(function(){
angular.module('masDistribucion.causes',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',CausesConfig])
.controller('CausesIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','CausesDataService',
            CausesIndexController
            ])

.controller('CausesEditController',
    ['$scope','$timeout','$state','$stateParams',
    'PARTIALPATH','ModalService','CatalogService','CausesDataService',
    'cause',
    CausesEditController
    ]);
})();