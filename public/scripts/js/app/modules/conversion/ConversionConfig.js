function ConversionConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('conversion', {
        url: "/conversion",
        views:{
            'main':{
                templateUrl: PARTIALPATH.conversion + '/index.html',
                controller: 'ConversionIndexController'
            }
        }
    })
    .state('conversion.edit', {
      url:"/{converId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.conversion + '/edit.html',
            controller: 'ConversionEditController',
            resolve: {
                conver: ['$stateParams','UtilsService','ConversionDataService',function($stateParams,UtilsService,ConversionDataService){                
                var data=ConversionDataService.getData();
                var conver= UtilsService.findById(data,$stateParams.converId)
                if(conver) return conver
                else{
                    return ConversionDataService.getConversion({id: $stateParams.converId})
                        .then(function(response){
                            if(response && angular.isArray(response.data) && response.data.length>0 && response.data[0].length>0)
                                return response.data[0][0];
                            else
                                return {};
                        })
                }
                
            }]
            }
        }
      }          
    })
    .state('conversion.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.conversion + '/edit.html',
                controller: 'ConversionEditController',
                resolve: {
                    conver: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}