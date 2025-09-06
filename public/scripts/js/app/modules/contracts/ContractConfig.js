function ContractConfig($stateProvider, $urlRouterProvider, $locationProvider, PARTIALPATH){
    
    $stateProvider //Index
    .state('contracts',{
        url:"/contracts",
            views:{
                'main':{
                    templateUrl: PARTIALPATH.contracts + 'index.html',
                    controller: 'ContractsIndexController'
                }
            }
    })
}