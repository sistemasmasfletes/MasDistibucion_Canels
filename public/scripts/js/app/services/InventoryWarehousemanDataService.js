function InventoryWarehousemanDataService(DataService,PATH){
    var data=[];
    
    this.setData=function(externalData){
        while(data.length > 0) {
            data.pop();
        }
        
    };

    this.getData=function(){
        return data;
    };
   
    this.getInventoryWarehouseman=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams;

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.inventoryWarehouseman + 'getInventoryWarehouseman',params,null);
    };
}