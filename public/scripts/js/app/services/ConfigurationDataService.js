function ConfigurationDataService(DataService,PATH){
	var data=[];
    
    this.setData=function(externalData){
    	if(!angular.isArray(data)) return [];
        while(data.length > 0) {
            data.pop();
        }
        data = externalData.slice();
    }

    this.getData=function(){
        return data;
    }

    this.findById=function(a, id) {        
        for (var i = 0; i < a.length; i++) {
            if (a[i].id == id) return a[i];
        }
        return null;
    }

	this.getConfiguration = function(paramsData,configData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: '', sortDir: ''};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.configuration +'getConfiguration',params,configData);
    }

    this.save = function(paramsData,configData){
        return DataService.save(PATH.configuration +'save',paramsData,configData);
    }
}