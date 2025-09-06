function BancosDataService(DataService,PATH){
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
    
    this.getBancos=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.bancos +'getBancos',params,null);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.bancos + 'save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.bancos + 'delete',data,httpParams);
    }

    this.getBancosByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.bancos + '/getBancoCatalog',params,null);
    }
}