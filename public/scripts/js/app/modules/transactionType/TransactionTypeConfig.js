function TransactionTypeConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('transactionType', {
        url: "/transactionType",
        views:{
            'main':{
                templateUrl: PARTIALPATH.transactionType + 'index.html',
                controller: 'TransactionTypeIndexController'
            }
        }
    })
    .state('transactionType.edit', {
      url:"/{transactionId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: PARTIALPATH.transactionType + 'edit.html',
            controller: 'TransactionTypeEditController',
            resolve: {
                transaction: ['$stateParams','UtilsService','TransactionTypeDataService',function($stateParams,UtilsService,TransactionTypeDataService){                
                var data=TransactionTypeDataService.getData();
                var transaction = UtilsService.findById(data,$stateParams.transactionId)
                if(transaction) return transaction
                else{
                    return TransactionTypeDataService.getTransactionTypeById({id: $stateParams.transactionId})
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
    .state('transactionType.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: PARTIALPATH.transactionType + 'edit.html',
                controller: 'TransactionTypeEditController',
                resolve: {
                    transaction: function(){
                        return {};                                  
                    }
                }
            }
        }          
    });
}