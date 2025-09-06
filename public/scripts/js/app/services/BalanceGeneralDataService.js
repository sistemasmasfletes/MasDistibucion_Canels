function BalanceGeneralDataService(DataService,PATH){
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
    
    this.getCompraCreditos=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'cliente', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.balanceGeneral +'/getCompraCreditos',params,null);
    }

    

    this.getBalanceGeneralByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.balanceGeneral + '/getBalanceGeneralCatalog',params,null);
    }
}