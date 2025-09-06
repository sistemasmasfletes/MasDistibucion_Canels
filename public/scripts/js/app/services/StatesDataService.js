function StatesDataService(DataService,PATH) {
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

    this.getStates=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.states +'getStates',params,null);
    }

    this.save=function(url,data,httpParams){
        return DataService.save(url,data,httpParams);
    }

    this.delete=function(url,data,httpParams){
        return DataService.delete(url,data,httpParams);
    }
}