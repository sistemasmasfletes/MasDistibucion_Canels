function CuentasDataService(DataService,PATH){
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
    
    this.getCuentas = function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'cuenta', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.cuentas +'/getCuentas',params,null);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.cuentas + '/save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.cuentas + '/delete',data,httpParams);
    }

    this.getCuentasByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.cuentas + '/getCuentasCatalog',params,null);
    }
}