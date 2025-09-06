function TransactionTypeDataService(DataService,PATH){
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
    
    this.getTransactionType=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.transactionType +'getTransactionType',params,null);
    }
    
     this.getTransactionTypeById=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.transactionType +'getTransactionTypeById',params,null);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.transactionType + 'save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.transactionType + 'delete',data,httpParams);
    }

    this.getTransactionTypeByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.transactionType + 'getTransactionTypeCatalog',params,null);
    }
}