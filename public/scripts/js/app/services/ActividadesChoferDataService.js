function ActividadesChoferDataService(DataService,PATH){
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
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'fecha', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.actividadesChofer +'/getCompraCreditos',params,null);
    }

    

    this.getActividadesChoferByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.actividadesChofer + '/getActividadesChoferCatalog',params,null);
    }
}