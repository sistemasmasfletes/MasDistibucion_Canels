function CausesDataService(DataService,PATH){
    var data=[];
    
    this.setData=function(externalData){
        while(data.length > 0) {
            data.pop();
        }
        data = externalData.slice();
    }

    this.getData=function(){
        return data;
    }
    
    this.getCauses=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.causes +'/getCauses',params,null);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.causes + '/save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.causes + '/delete',data,httpParams);
    }

    this.getCausesByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.causes + '/getCausesCatalog',params,null);
    }
}