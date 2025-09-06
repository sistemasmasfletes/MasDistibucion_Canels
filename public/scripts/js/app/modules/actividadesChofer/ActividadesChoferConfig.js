function ActividadesChoferConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('actividadesChofer', {
        url: "/actividadesChofer",
        views:{
            'main':{
                templateUrl: PARTIALPATH.actividadesChofer + 'index.html',
                controller: 'ActividadesChoferIndexController'
            }
        }
    });
    
}