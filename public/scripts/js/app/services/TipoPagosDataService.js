function TipoPagosDataService(DataService,PATH){
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
    
    this.getTipoPagos=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'tipoPago', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.tipoPagos +'/getTipoPagos',params,null);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.tipoPagos + 'save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.tipoPagos + 'delete',data,httpParams);
    }

    this.getTipoPagosByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.tipoPagos + '/getTipoPagosCatalog',params,null);
    }
}