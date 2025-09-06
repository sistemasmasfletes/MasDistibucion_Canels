function BalanceGeneralConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('balanceGeneral', {
        url: "/balanceGeneral",
        views:{
            'main':{
                templateUrl: PARTIALPATH.balanceGeneral + 'index.html',
                controller: 'BalanceGeneralIndexController'
            }
        }
    });
    
}