function AdministracionLogClientDataService(DataService,PATH){
    var data=[];
    
    this.setData=function(externalData){
        while(data.length > 0) {
            data.pop();
        }
        data = externalData.slice();
    };

    this.getData=function(){
        return data;
    };
    
    this.getAdministracionLogCliente=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams;        

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.administracionLogClient +'/getAdministracionLogCliente',params,null);
    };

    

    this.getAdministracionLogClienteByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.administracionLogClient + '/getAdministracionLogClienteCatalog',params,null);
    };
}

