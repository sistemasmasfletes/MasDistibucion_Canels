function AdministracionLogClienteConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider

    .state('administracionLogCliente', {
        url: "/administracionLogCliente",
        views:{
            'main':{
                templateUrl: PARTIALPATH.administracionLogCliente + 'index.html',
                controller: 'AdministracionLogClienteIndexController'
            }
        }
      
    });
    
}