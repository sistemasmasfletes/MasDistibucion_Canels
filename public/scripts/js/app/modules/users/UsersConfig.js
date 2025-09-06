function UsersConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('users', {
        url: "/users",
        views:{
            'main':{
                templateUrl: PARTIALPATH.users + 'index.html',
                controller: 'UsersIndexController'
            }
        }
    })
    .state('users.edit', {
      url:"/{userId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.users + 'edit.html',
            controller: 'UsersEditController',
            resolve: {
                user: ['$stateParams','UtilsService','UsersDataService',function($stateParams,UtilsService,UsersDataService){                
                var data=UsersDataService.getData();
                var user = UtilsService.findById(data,$stateParams.userId)
                if(user) return user
                else{
                    return UsersDataService.getUsers({id: $stateParams.userId})
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
    .state('users.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.users + 'edit.html',
                controller: 'UsersEditController',
                resolve: {
                    user: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}