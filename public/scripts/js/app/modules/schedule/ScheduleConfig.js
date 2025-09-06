function ScheduleConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('schedule', {
        url: "/schedule",
        views:{
            'main':{
                templateUrl: PARTIALPATH.schedule + 'index.html',
                controller: 'ScheduleIndexController'
            }
        }
    })
    .state('schedule.detail',{
        url:"/route/{routeId:[0-9]{1,6}}/{triggerReload}",
        views:{
            'scheduleDetail':{
                templateUrl: PARTIALPATH.schedule + 'scheduleDetail.html',
                controller: 'ScheduleDetailController'
            }
        },
        params:{reload:false,selectedId:null}  
    })
    .state('schedule.detail.edit', {
      url:"schedule/{scheduleId:[0-9]{1,6}}",
      views:{
        'edit@schedule': {
            templateUrl: PARTIALPATH.schedule + 'edit.html',
            controller: 'ScheduleEditController',
            resolve: {
                schedule: ['$stateParams','UtilsService','ScheduleDataService',function($stateParams,UtilsService,ScheduleDataService){                
                var data=ScheduleDataService.getData();
                var schedule = UtilsService.findById(data,$stateParams.scheduleId)
                if(schedule) return schedule
                else{
                    return ScheduleDataService.getScheduleDetail({id: $stateParams.scheduleId})
                        .then(function(response){
                            console.log(response);
                            if(response.data && response.data.data.length>0)                                
                                return response.data.data[0];
                                
                            else
                                return {route_id:null,vehicle_id:null,user_id:null, monday:0, tuesday:0, wednesday:0, thursday:0, friday:0, saturday:0, sunday:0, status:1, week:0, recurrent:0};
                        }) 
                }
                
            }]
            }
        }
      }          
    })
    .state('schedule.detail.scheduledDates', {
        url:"scheduledDates/{scheduleId:[0-9]{1,6}}/",
        views:{
        'scheduledDates@schedule': {
            templateUrl: PARTIALPATH.schedule + 'scheduledDates.html',
            controller: 'ScheduledDatesController'            
        }
      }          
    })
    .state('schedule.detail.scheduledDates.edit', {
        url:"schedDate/{schedDatesId:[0-9]{1,6}}",
        views:{
        'scheduledDatesEdit@schedule': {
            templateUrl: PARTIALPATH.schedule + 'scheduledDatesEdit.html',
            controller: 'ScheduledDatesEditController',
            resolve: {
                schedule : ['$stateParams','UtilsService','ScheduleDataService',function($stateParams,UtilsService,ScheduleDataService){
                    var data=ScheduleDataService.getLocalScheduledDates();
                    var schedule = UtilsService.findById(data,$stateParams.schedDatesId);
                    if(schedule) return schedule
                    else{
                        return ScheduleDataService.getScheduledDate({scheduledDateId: $stateParams.schedDatesId})
                            .then(function(response){
                                if(response.data)                                
                                    return response.data;                                    
                                else
                                    return {routeid:null,vehicleid:null,driverid:null,scheduled_date:null/*, monday:0, tuesday:0, wednesday:0, thursday:0, friday:0, saturday:0, sunday:0, status:1, week:0, recurrent:0*/};
                            }) 
                    }
                }]
            }
        }
      }          
    })
    .state('schedule.detail.add', {
        url:"schedule/add",
        views:{
            'edit@schedule': {
                templateUrl: PARTIALPATH.schedule + 'edit.html',
                controller: 'ScheduleEditController',
                resolve: {
                    schedule: ['$stateParams','RoutesDataService',function($stateParams,RoutesDataService){
                        return RoutesDataService.getRoutes({id: $stateParams.routeId})
                        .then(function(response){
                            if(response && angular.isArray(response.data) && response.data.length>0 && response.data[0].length>0){
                                var route = response.data[0][0];
                                return {route_id:route.id,vehicle_id:null,user_id:null, monday:0, tuesday:0, wednesday:0, thursday:0, friday:0, saturday:0, sunday:0, status:1, week:0, recurrent:0, route:'['+route.code+'] '+route.name, vehicle:null, driver:null};
                            }
                            else
                                return {};
                        });
                    }]
                }
            }
        },
        params:{routeId:null}
    });
}