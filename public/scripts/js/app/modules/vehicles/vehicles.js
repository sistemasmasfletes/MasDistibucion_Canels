(function(){
angular.module('masDistribucion.vehicles',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',VehiclesConfig])
.controller('VehiclesIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','VehiclesDataService',
            VehiclesIndexController
            ])

.controller('VehiclesEditController',
    ['$scope','$timeout','$state','$stateParams',
    'PARTIALPATH','ModalService','CatalogService','VehiclesDataService',
    'vehicle',
    VehiclesEditController
    ]);
})();