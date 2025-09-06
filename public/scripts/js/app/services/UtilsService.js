function UtilsService(){

    this.findById=function(a, id) {        
        for (var i = 0; i < a.length; i++) {
            if (a[i].id == id) return a[i];
        }
        return null;
    }

    this.getDateFromString = function(strDate){                                               
        //Se asume que la fecha viene en formato yyyy-mm-dd hh:MM:ss
        if(!strDate) return null;
        var dt = strDate.split(/[- :]/);
        return new Date(dt[0], dt[1] - 1, dt[2], dt[3] || 0, dt[4] || 0, dt[5] || 0);

    }
    
    this.getTimeFromString = function(strDate){
        //Se asume que la fecha viene en formato yyyy-mm-dd hh:MM:ss
        if(!strDate) return null;
        var h,m,s;
        
        y = parseInt(strDate.substring(0,4));
        m = parseInt(strDate.substring(5,7))-1;
        d = parseInt(strDate.substring(8));
        return new Date(y,m,d);
    }

    this.getKeysFromJsonOnject=function(jsonObject){
        var keys=[]
        for(i in jsonObject){            
            keys.push(i);
        }
        return keys;
    }

    this.createNgTablePostParams = function(ngTableParams, extraParams){
        var postParams = {page:ngTableParams.page(), rowsPerPage:ngTableParams.count()};
        var filter = ngTableParams.filter();
        var sorting = ngTableParams.sorting();
        var sortField = this.getKeysFromJsonOnject(sorting)[0];

        if(extraParams) angular.extend(postParams,extraParams);
        if(sorting) angular.extend(postParams,{sortField:sortField, sortDir:sorting[sortField]});
        if(filter) angular.extend(postParams,{filter:filter});

        return postParams;
    }

    this.changePropOnArrayRow = function(objectArray,searchProp, searchValue, setProp,setValue,compareMode){
        if(!angular.isArray(objectArray)) return;

        for (var i = 0; i < objectArray.length; i++) {
            if(compareMode == '=='){
                if(!objectArray[i][setProp]) return;
                if (objectArray[i][searchProp] == searchValue) {
                    objectArray[i][setProp] = setValue;
                    break;
                }
            }else if(compareMode == '!='){                
                if (objectArray[i][searchProp] != searchValue) {
                    objectArray[i][setProp] = setValue;                   
                }
            }
        }
        
    }

}