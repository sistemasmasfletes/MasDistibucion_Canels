(function(){
angular.module('masDistribucion.users',[
    "ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',UsersConfig])
.controller('UsersIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','UsersDataService',
            UsersIndexController
            ])
.controller('UsersEditController',
    ['$scope','$timeout','$state','$stateParams',
    'PARTIALPATH','ModalService','CatalogService','UsersDataService',
    'user',
    UsersEditController
    ]);
})();
