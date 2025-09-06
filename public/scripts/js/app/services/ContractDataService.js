function ContractDataService(DataService,PATH) {
    var data=[];
    
    this.getContracts=function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;
        
        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.contracts +'getContracts',params,null);
    };

    this.setAcept=function(paramsData){
        return DataService.get(PATH.contracts +'setAcept',paramsData,null);
    };
    
    this.upContract=function(paramsData){
        return DataService.get(PATH.contracts +'upContract',paramsData,null);
    };
}