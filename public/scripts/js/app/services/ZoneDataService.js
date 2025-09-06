function ZoneDataService(DataService, PATH){
    var data = [];
    
    this.setData = function(externalData){
        while(data.length > 0 ){
            data.pop();
        }
        data = externalData.slice();
    };
    
    this.getData = function(){
        return data;
    };
    
    this.getZone = function(paramsData, config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'DESC'};
        if(!paramsData){
            paramsData = defaultParams;
        }
        var params = angular.extend({}, defaultParams, paramsData);
        return DataService.get(PATH.zone + 'getZone', params, config);
    }
    
    this.delete = function(paramsData, configData){
        return DataService.delete(PATH.zone + 'delete', paramsData, configData);
    }
}