function PromotionReceivedDataService(DataService,PATH){
	var data=[];
    
    this.setData=function(externalData){
    	if(!angular.isArray(data)) return [];
        while(data.length > 0) {
            data.pop();
        }
        data = externalData.slice();
    }

    this.getData=function(){
        return data;
    }

    this.findById=function(a, id) {        
        for (var i = 0; i < a.length; i++) {
            if (a[i].id == id) return a[i];
        }
        return null;
    }

	this.getReceived = function(paramsData,configData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.promotionReceived +'getPromotionReceived',params,configData);
    }

    this.saveSurvey = function(paramsData,configData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},paramsData);

        return DataService.get(PATH.promotionSchedule +'saveSurvey',params,configData);
    }
    
    this.save=function(data,httpParams){
        return DataService.save(PATH.routeSummary + 'save',data,httpParams);
    }
    
    this.updateStatusPromotion = function(params){
        return DataService.get(PATH.promotionSchedule + 'updateStatusPromotion',params,null);
    }    
    
    this.saveHourPoint = function (params){
        return DataService.saveHourPoint(PATH.routeSummary + 'saveHourPoint',params);
    }    
    
}