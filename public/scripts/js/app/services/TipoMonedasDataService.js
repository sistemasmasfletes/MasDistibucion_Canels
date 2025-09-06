function TipoMonedasDataService(DataService,PATH){
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
    
    this.getTipoMonedas=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'moneda', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.tipoMonedas +'/getTipoMonedas',params,null);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.tipoMonedas + '/save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.tipoMonedas + '/delete',data,httpParams);
    }

    this.getTipoMonedasByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.tipoMonedas + '/getTipoMonedasCatalog',params,null);
    }
}