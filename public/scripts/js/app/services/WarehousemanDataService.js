function WarehousemanDataService (DataService,PATH){
    var data=[];
    
    this.setData=function(externalData){
        while(data.length > 0) {
            data.pop();
        }

    }

    this.getData=function(){
        return data;
    }
    
    this.getViewWarehousemanRoutes=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.warehouseman + 'getViewWarehousemanRoutes',params,null);
    }
    
    this.getWarehouse=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.warehouseman + 'getWarehouse',params,null);
    }
    
    this.getWarehousePacks=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.warehouseman + 'getWarehousePacks',params,null);
    }
    
    this.getWarehousemanActivity=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.warehouseman + 'getWarehousemanActivity',params,null);
    }
    
    this.save=function(data,httpParams){
        return DataService.save(PATH.warehouseman + 'save',data,httpParams);
    }
    
    this.getPackageTrackingWarehouseman=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.warehouseman + 'getPackageTrackingWarehouseman',params,null);
    }
 
    /****************************RECHAZO DE PAQUETES********************************************/
    this.getPackageRejected=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.warehouseman + 'getPackageRejected',params,null);
    }
    
    this.getPackageRejectedById=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
    	var params = angular.extend({},defaultParams,paramsData);
        return DataService.get(PATH.warehouseman + 'getPackageRejectedById',params,null);
    }
    
    this.getScheduleRejected=function(paramsData){
    	var paramsData = {pointId:paramsData}
    	return DataService.get(PATH.warehouseman + 'getScheduleRejected',paramsData,null);
    }
    
    this.setScheduleRejected=function(paramsData){
    	var paramsData = {Oid:paramsData}
    	return DataService.get(PATH.warehouseman + 'setScheduleRejected',paramsData,null);
    }
    /****************************RECHAZO DE PAQUETES********************************************/
}
