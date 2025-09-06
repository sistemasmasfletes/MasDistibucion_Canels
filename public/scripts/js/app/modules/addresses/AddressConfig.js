function AddressConfig($stateProvider, $urlRouterProvider, $locationProvider, CONFIG){
    
    $stateProvider //Index
    .state('addresses',{
        url:"/addresses",
            views:{
                'main':{
                    templateUrl: CONFIG.PARTIALS + 'addresses/index.html',
                    controller: 'AddressIndexController'
                }
            }
    })
    
    .state('addresses.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: CONFIG.PARTIALS + 'addresses/edit.html',
                controller: 'AddressesEditController',
                resolve: {
                    address: function(){
                        return {};                                  
                    }
                }
            }
        }          
    })
    
    .state('addresses.edit', {
      url:"/{addressId:[0-9]{1,9}}",
      views:{
        'edit': {
            templateUrl: CONFIG.PARTIALS + 'addresses/edit.html',
            controller: 'AddressesEditController',
            resolve: {
                address: ['$stateParams','UtilsService','AddressDataService',function($stateParams,UtilsService,AddressDataService){                
                var data=AddressDataService.getData();
                var address = UtilsService.findById(data,$stateParams.addressId)
                if(address) return address
                else{
                    
                    return AddressDataService.getAddressById({id: $stateParams.addressId})
                        .then(function(response){
                            if(response.data && response.data.data.length>0)
                                return response.data.data[0];
                            else
                                return {};
                        })
                }
                
            }]
            }
        }
      }          
    });
}