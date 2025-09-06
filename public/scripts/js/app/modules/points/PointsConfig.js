function PointsConfig($stateProvider, $urlRouterProvider, $locationProvider, CONFIG){
    
    $stateProvider
    .state('points',{
        url:"/points",
        views:{
            'main':{
                templateUrl: CONFIG.PARTIALS + 'points/index.html',
                controller: 'PointsIndexController'
            }
        }
    })
    
    
    .state('points.edit', {
      url:"/{pointId:[0-9]{1,6}}",
      views:{
        'edit': {
            templateUrl: CONFIG.PARTIALS + 'points/edit.html',
            controller: 'PointsEditController',
            resolve: {
                point: ['$stateParams','UtilsService','PointsDataService',function($stateParams,UtilsService,PointsDataService){                
                var data=PointsDataService.getData();
                var point = UtilsService.findById(data,$stateParams.pointId)
                if(point) return point
                else{
                    return PointsDataService.getPointById({id: $stateParams.pointId})
                        .then(function(response){
                            if(response.data && response.data.data.length>0)
                                return response.data.data[0];
                            else
                                return {};
                        })
                        console.log
                }
                
            }]
            }
        }
      }
    })
    .state('points.add', {
        url:"/add",
        views:{
            'edit': {
                templateUrl: CONFIG.PARTIALS + 'points/edit.html',
                controller: 'PointsEditController',
                resolve: {
                    point: function(){
                        return {};                                  
                    }
                }
            }
        }          
    })

    .state('points.contact',{
        url:"/contact/{pointId}",
        views:{
            'contact': {
                templateUrl: CONFIG.PARTIALS + 'points/contact.html',
                controller: 'ContactIndexController',
                resolve: {
                    point: function(){
                        return {};
                    }
                }
            }
        }
    })
    
    .state('points.contact.add',{
        url:"/add",
        views:{
            'contactEdit':{
                templateUrl: CONFIG.PARTIALS + 'points/contactEdit.html',
                controller: 'ContactEditController',
                resolve: {
                    contact: function(){
                        return;
                    }
                }
            }
        }
    })
    
    .state('points.contact.edit',{
        url:"/edit/{contactId}",
        views:{
          'contactEdit': {
              templateUrl: CONFIG.PARTIALS + 'points/contactEdit.html',
              controller: 'ContactEditController',
              resolve: {
                  contact: ['$stateParams','UtilsService','PointsDataService',function($stateParams,UtilsService,PointsDataService){                
                  var data=PointsDataService.getData();
                  var contact = UtilsService.findById(data,$stateParams.contactId)
                  if(contact) return contact;
                  
                  else{
                      return PointsDataService.getContactById({id: $stateParams.contactId})
                          .then(function(response){
                              if(response.data && response.data.data.length>0){ 
                                return response.data.data[0];
                            }else{
                                  return {};}
                          })
                  }

              }]
              }
          }
        }
    })
    
    .state('points.classification',{
        url:"/classification/{pointId}",
        views:{
          'classification': {
              templateUrl: CONFIG.PARTIALS + 'points/classification.html',
              controller: 'ClassificationEditController',
              resolve: {
                  classification: ['$stateParams','UtilsService','PointsDataService',function($stateParams,UtilsService,PointsDataService){                
                  var data=PointsDataService.getData();
                  var classification = UtilsService.findById(data,$stateParams.pointId)
                  if(classification) return classification;
                  
                  else{
                      return PointsDataService.getClassificationById({id: $stateParams.pointId})
                          .then(function(response){
                              if(response.data && response.data.data.length>0){ 
                                return response.data.data[0];
                            }else{
                                  return {};}
                          })
                  }

              }]
              }
          }
        }
    });
    
    /*.state('points.classification',{
        url:"/classification/{pointId}",
        views:{
            'classification': {
                templateUrl: CONFIG.PARTIALS + 'points/classification.html',
                controller: 'ClassificationEditController',
                resolve: {
                    classification: function(){
                        return {};
                    }
                }
            }
        }
    });*/
}