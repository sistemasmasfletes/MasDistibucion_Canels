function InventoryConfig($stateProvider, $urlRouterProvider,$locationProvider, CONFIG)
{
    $stateProvider
    .state('inventory', {
        url: "/inventory",
        views:{
            'main':{
                templateUrl: CONFIG.PARTIALS + 'inventory/index.html',
                controller: 'InventoryIndexController'
            }
        }
    })
    .state('inventory.view', {
      url:"/packs/{id:[0-9]{1,8}}",
      views:{
        'edit': {
            templateUrl: CONFIG.PARTIALS + 'inventory/edit.html',
            controller: 'InventoryPacksController'

            }
        }
    });
}