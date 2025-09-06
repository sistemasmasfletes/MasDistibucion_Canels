(function(){
angular.module('masDistribucion.routes',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',RoutesConfig])
.controller('RoutesIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','CatalogService','RoutesDataService','routes',
            RoutesIndexController
            ])

.controller('RoutesEditController',
    ['$scope','$cookieStore','$timeout','$state','$stateParams',
    'PARTIALPATH','ModalService','JQGridService','CatalogService','RoutesDataService',
    'route',
    RoutesEditController
    ])

.controller('RoutesEditPointsController',
    ['$scope','$cookieStore','$timeout','$state','$stateParams','$compile',
    'PATH','PARTIALPATH','ModalService','JQGridService','CatalogService','RoutesDataService','PointsDataService',
    'points',
    RoutesEditPointsController
    ]);
})();