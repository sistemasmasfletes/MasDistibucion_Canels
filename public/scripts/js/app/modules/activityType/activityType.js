(function(){
angular.module('masDistribucion.activityType',[
	"ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',ActivityTypeConfig])
.controller('ActivityTypeIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','ActivityTypeDataService',
            ActivityTypeIndexController
            ])

.controller('ActivityTypeEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PARTIALPATH','ModalService','CatalogService','ActivityTypeDataService',
            'activity',
            ActivityTypeEditController
            ]);
})();