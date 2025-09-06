function CardOperatorsDataService(DataService, PATH){
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
    
    this.getCardOperator = function(paramsData, config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'chrOperator', sortDir: 'DESC'};
        if(!paramsData){
            paramsData = defaultParams;
        }
        var params = angular.extend({}, defaultParams, paramsData);
        return DataService.get(PATH.cardOperators + 'getCardOperators', params, config);
    }
    
    this.delete = function(paramsData, configData){
        return DataService.delete(PATH.cardOperators + 'delete', paramsData, configData);
    }
}