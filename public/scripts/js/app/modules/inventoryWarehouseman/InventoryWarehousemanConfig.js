function InventoryWarehousemanConfig($stateProvider, $urlRouterProvider,$locationProvider, CONFIG)
{
    $stateProvider
    .state('inventoryWarehouseman', {
        url: "/inventoryWarehouseman",
        views:{
            'main':{
                templateUrl: CONFIG.PARTIALS + 'inventoryWarehouseman/index.html',
                controller: 'InventoryWarehousemanController'
            }
        }
    });
}