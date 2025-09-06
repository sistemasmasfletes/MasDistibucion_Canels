(function(){
angular.module('masDistribucion.conversion',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',ConversionConfig])
.controller('ConversionIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','ConversionDataService',
            ConversionIndexController
            ])

.controller('ConversionEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','CatalogService','ConversionDataService',
            'conver','$http',
            ConversionEditController
            ]);
})();