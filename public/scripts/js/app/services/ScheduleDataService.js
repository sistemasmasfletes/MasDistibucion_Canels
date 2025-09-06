function ScheduleDataService(DataService,PATH){
    var data=[];
    var localScheduledDates=[];

    this.setData=function(externalData){
        while(data.length > 0) {
            data.pop();
        }
        data = externalData.slice();
    }

    this.getData=function(){
        return data;
    }
    
    this.setLocalScheduledDates=function(externalData){
        while(localScheduledDates.length > 0) {
            localScheduledDates.pop();
        }
        localScheduledDates = externalData.slice();
    }

    this.getLocalScheduledDates=function(){
        return localScheduledDates;
    }

    this.getSchedule=function(paramsData,config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'routeName', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.schedule +'getSchedules',params,config);
    }

    this.getScheduleDetail=function(paramsData,config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'start_date', sortDir: 'desc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.schedule +'getScheduleDetail',params,config);
    }

    this.getScheduledDates=function(paramsData,config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'scheduled_date', sortDir: 'desc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.schedule +'getScheduledDates',params,config);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.schedule + 'save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.schedule + 'delete',data,httpParams);
    }

    this.getScheduledDate=function(paramsData,config){
        return DataService.get(PATH.schedule +'getScheduledDate',paramsData,config);
    }

    this.updateScheduledDate = function(data,httpParams) {
        return DataService.save(PATH.schedule + 'updateScheduledDate',data,httpParams);
    }
}