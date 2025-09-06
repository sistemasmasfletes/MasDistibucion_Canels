function CatalogService(DataService,PATH){
    this.getPointStatus=function(){
        return [
               {id:0, status:'Sin estatus'},
               {id:1, status:'Normal'},
               {id:2, status:'Pausado'},
               {id:3, status:'Cancelado'}
            ];
    }

    this.getPointAvailability=function(){
        return [
           {id:1, tipo:'Obligatorio'},
           {id:0, tipo:'No obligatorio'}
       ];
    }
    
    this.getPointMin=function(){
    	var arr = new Array();
        for (var i = 0; i < 60; i++){
        	var a = i.toString()
        	obj = {id:i,tipo:a};
        	arr.push(obj);
        }
        return arr;
    }

    this.getRouteStatus = function(){
        return [{id:0, status:'Inactiva'},{id:1, status:'Activa'}];
    }

    this.getVehicleType=function(){
        return [{id:0, tipo:'Desconocido'},{id:1,tipo:'Caja Seca'}]
    }

    this.getVehicleStatus=function(){
        return [{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
    this.getPointType=function(){
        return [{id:1, tipo:'Punto de venta'},{id:2,tipo:'Centro de intercambio'}];
    }

    this.getCauseStatus=function(){
        return [{id:0, status:''},{id:1, status:''}];
    }
    
    this.getCauseType=function(){
        return [{id:0, tipo:''},{id:1,tipo:''}]
    }
    
    this.getActivityTypeStatus=function(){
        return [{id:0, status:''},{id:1, status:''}];
    }
    
    this.getActivityTypeType=function(){
        return [{id:0, tipo:''},{id:1,tipo:''}];
    }
 
    this.getTransactionTypeStatus=function(){
        return [{id:0, status:''},{id:1, status:''}];
    }
    
    this.getTransactionTypeType=function(){
        return [{id:0, tipo:''},{id:1,tipo:''}]
    }

    this.getUserType=function(){
        return DataService.get(PATH.users +'getRoleCatalog', {},{});
    	/*return [
    	        {id:1, tipo:"Administrador", labelRoute:"Admin", gridCaption:"Administradores"},
		        {id:2, tipo:"Conductor", labelRoute:"Driver", gridCaption:"Conductores"},
		        {id:3, tipo:"Cliente", labelRoute:"Client", gridCaption:"Clientes"},
		        {id:4, tipo:"Secretaria", labelRoute:"Secretary", gridCaption:"Secretarias"},
		        {id:6, tipo:"Almacenista",labelRoute:"Storer", gridCaption:"Almacenistas"},
		        {id:7, tipo:"Controlador de Operaciones", labelRoute:"OperationController", gridCaption:"Controladores de Operación"},
		        {id:8, tipo:"Director Ejecutivo", labelRoute:"CEO", gridCaption:"Directores Ejecutivos"},
		        ];*/
    }

    this.getUserStatus = function(){
      return [
        {id:0,status:"Inactivo"},
        {id:1,status:"Activo"},
        {id:2,status:"Bloqueado"}
        ];
    }

    this.getRouteSummaryStatus = function(op){
    	switch (op){
    		case 'Recolectar':
    			var $op =[
    		        {id:1,status:"Recibido"}
    		        ];	
    			break;
    		case 'Entrega':
			var $op =[
	    		        {id:3,status:"Entregado"},
	    		        {id:6,status:"Rechazado"}
	    		        ];	
    			
    			break;
    		default:
    			var $op =[
    	    		        {id:1,status:"Recibido"},
    	    		        {id:3,status:"Entregado"},
    	    		        {id:6,status:"Rechazado"}
    	    		        ];	
    	}
      return $op;
    }

    this.getBranchCategories =function(){
        return DataService.get(PATH.users +'getCategories',{},{});
    }
    
    this.getCatalogCauses = function(){
        return DataService.get(PATH.causes +'/getCausesEvidence',{},{});
    }

    this.getCatalogUsers = function(){
        return DataService.get(PATH.warehouseman +'getUserDelivery',{},{});
    }
    
    this.getTransferStatus = function(){
        return [
            {id:1,status:"exitosa"},
            {id:2,status:"fallida"}
        ];
    }
    
    this.getBancoType=function(){
        return [{id:0, tipo:'Desconocido'},{id:1,tipo:'Caja Seca'}]
    }

    this.getBancoStatus=function(){
        return [{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
    this.getTipoPagosType=function(){
        return [{id:0, tipo:'Desconocido'},{id:1,tipo:'Caja Seca'}]
    }

    this.getTipoPagosStatus=function(){
        return [{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
    this.getTipoMovimientosType=function(){
        return[{id:0, tipo:'Desconocido'},{id:1, tipo:'Caja Seca'}];
    }
    
    this.getTipoMovimientosStatus=function(){
        return[{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
    this.getEstatusType=function(){
        return[{id:0, tipo:'Desconocido'},{id:1, tipo:'Caja Seca'}];
    }
    
    this.getEstatusStatus=function(){
        return[{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
     this.getTipoMonedasType=function(){
        return[{id:0, tipo:'Desconocido'},{id:1, tipo:'Caja Seca'}];
    }
    
    this.getTipoMonedasStatus=function(){
        return[{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
     this.getPaisesType=function(){
        return[{id:0, tipo:'Desconocido'},{id:1, tipo:'Caja Seca'}];
    }
    
    this.getPaisesStatus=function(){
        return[{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
     this.getCuentasType=function(){
        return[{id:0, tipo:'Desconocido'},{id:1, tipo:'Caja Seca'}];
    }
    
    this.getCuentasStatus=function(){
        return[{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
     this.getConversionType=function(){
        return[{id:0, tipo:'Desconocido'},{id:1, tipo:'Caja Seca'}];
    }
    
    this.getConversionStatus=function(){
        return[{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
     this.getCompraCreditosType=function(){
        return[{id:0, tipo:'Desconocido'},{id:1, tipo:'Caja Seca'}];
    }
    
    this.getCompraCreditosStatus=function(){
        return[{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
     this.getAprobacionCreditosType=function(){
        return[{id:0, tipo:'Desconocido'},{id:1, tipo:'Caja Seca'}];
    }
    
    this.getAprobacionCreditosStatus=function(){
        return[{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
    this.getTransferenciaCreditosType=function(){
        return[{id:0, tipo:'Desconocido'},{id:1, tipo:'Caja Seca'}];
    }
    
    this.getTransferenciaCreditosStatus=function(){
        return[{id:0, status:'Inactivo'},{id:1, status:'Activo'}];
    }
    
    this.getAddressAuthorization = function(){
        return [{id:0, status:'No autorizar'},{id: 1, status:'Autorizar'}];
    }
    
    this.getCatalogPoints = function(){
        return DataService.get(PATH.users +'getPoint', {},{});
    } 
    
    this.getCatalogMoneda = function(){
        return DataService.get(PATH.users +'getMoneda', {},{});
    } 
    
    this.getCatalogZona = function(){
        return DataService.get(PATH.users +'getZona', {},{});
    } 

    /************************************/
    this.getFranchisee = function(){
        return DataService.get(PATH.users +'getFranchisee', {},{});
    } 
    /************************************/
    this.getMaxpointId = function(){        return DataService.get(PATH.users +'getMaxpoint', {},{});    }     
    this.getCatalogZonaByController = function(){
       return DataService.get(PATH.users +'getZonaByController', {},{});
    }
    
    this.getZonaByUser = function(){
       return DataService.get(PATH.users +'getZonaByUser', {},{});
    }
    
    this.getUserRole = function(){
        return DataService.get(PATH.users + 'getUserRole', {}, {});
    }
    
//    this.getAddress = function(){
//        return DataService.get(PATH.points + 'getAddress', {},{});
//    }
    
    this.getContactStatus=function(){
        return [
               {id:1, status:'Normal'},
               {id:2, status:'Pausado'},
               {id:3, status:'Cancelado'}
            ];
}
    
    this.getSize=function(){
        return [
            {id:1, size:'De 1 a 15 personas'},
            {id:2, size:'De 16 a 50 personas'},
            {id:3, size:'Más de 50 personas'}
        ];
    }
    
    this.getActivity=function(){
        return [
            {id:1, activity:'Poca'},
            {id:2, activity:'Intermedia'},
            {id:3, activity:'Extensa'}
        ];
    }
    
    this.getConsumption=function(){
        return [
            {id:1, consumption:'Negocio sin consumo'},
            {id:2, consumption:'Negocio de servicios'},
            {id:3, consumption:'Fabrica o similar'}
        ];
    }

    this.getConsumerType=function(){
        return [
            {id:1, name:'No es consumidor'},
            {id:2, name:'Podría ser consumidor'},
            {id:3, name:'Es consumidor'},
            {id:4, name:'Es un gran consumidor'}
        ];
    }

    this.getInterestLevel=function(){
        return [
            {id:1, name:'No interesado'},
            {id:2, name:'Medianamente interesado'},
            {id:3, name:'Interesado'},
            {id:4, name:'Muy interesado'}
        ];
    }

    this.getRequest=function(){
       return [
            {id:1, name:'N/A'},
            {id:2, name:'Solicita llamada'},
            {id:3, name:'Solicita cita'},
            {id:4, name:'Solicita producto'}
        ]; 
    }
}
