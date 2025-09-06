function TipoMovimientosDataService(DataService,PATH){
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
    
    this.getTipoMovimientos=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'tipoMovimiento', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.tipoMovimientos +'/getTipoMovimientos',params,null);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.tipoMovimientos + '/save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.tipoMovimientos + '/delete',data,httpParams);
    }

    this.getTipoMovimientosByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.tipoMovimientos + '/getTipoMovimientosCatalog',params,null);
    }
}