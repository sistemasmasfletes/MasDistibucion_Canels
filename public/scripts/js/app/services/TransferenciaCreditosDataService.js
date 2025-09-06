function TransferenciaCreditosDataService(DataService,PATH){
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
    
    this.getTransferenciaCreditos=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'cliente', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.transferenciaCreditos +'/getTransferenciaCreditos',params,null);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.transferenciaCreditos + '/save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.transferenciaCreditos + '/delete',data,httpParams);
    }

    this.getTransferenciaCreditosByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.transferenciaCreditos + '/getTransferenciaCreditosCatalog',params,null);
    }
    
    this.getClienteByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.transferenciaCreditos +'/clientes',params,null);
    }
}