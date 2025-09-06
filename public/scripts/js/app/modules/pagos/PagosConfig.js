function PagosConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('pagos', {
        url: "/pagos",
        views:{
            'main':{
                templateUrl: PARTIALPATH.pagos + 'index.html',
                controller: 'PagosIndexController'
            }
        }
    });
    
}