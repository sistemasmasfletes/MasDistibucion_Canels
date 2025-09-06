function UsersIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,UsersDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {}
    $scope.grid.refresh = grdRefresh;
    $scope.grid.edit = function(){grdEdit($scope.userId)};
    $scope.grid.delete = function(){grdDelete($scope.userId)};
    $scope.grid.add = grdAdd;

    $scope.grid.config = JQGridService.config({
    url: PATH.users + 'getUsers',  
    colNames: ["id", "Clave", /*"Nombre Comercial", "Categoría", */"Nombre", "Apellidos","Tel.", "Usuario", "Tipo de Usuario"],
    styleUI: 'Bootstrap',
    colModel: [
        {name: "id", width: 40, align: "right", hidden: true},
                {name: "code", index: "code", width: 60},
                /*{name: "commercial_name", index: "commercial_name", width: 120},
                {name: "category", index: "category", width: 80},*/
        {name: "first_name", index: "first_name", width: 80},
                {name: "last_name", index: "last_name", width: 100},                {name: "cell_phone", index: "tipo", width: 50},                
        {name: "username", index: "username", width: 50},
                {name: "tipo", index: "tipo", width: 50}                
    ],
    sortname: "first_name",
    sortorder: "asc",
    caption: 'Usuarios',
        beforeRequest: function(){
            $scope.loading = true;
        },
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.userId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {
            $scope.loading = false;
            $timeout(function(){
                UsersDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.userId = null;
            },0);
            JQGridService.resize('grdUsers');
        },
        serializeGridData: function(postData) {
            return JSON.stringify(postData);
        }
    },{id: "id"});

    
    
//   ngTableParams = $injector.get('ngTableParams');
//    UtilsService = $injector.get('UtilsService');
//    timeout =  $injector.get('$timeout');
//    
//    
//    $scope.isLoading=false;
//    $scope.partials = PARTIALPATH.base;
//
//    $scope.grid={};
//    $scope.grid.delete = function(){grdDelete($scope.userId)};
//    $scope.grid.add = grdAdd;
//    $scope.grid.edit = function(){return grdEdit($scope.userId)};
//
//    $scope.tableParams = new ngTableParams(
//        {   page:1,
//            count:10,
//            sorting:{
//                first_name:'asc'
//            }
//        },
//        {
//            total:0,
//            getData:function($defer,params){
//                var postParams = UtilsService.createNgTablePostParams(params);
//                
//                UsersDataService.getUsers(postParams)
//                .then(function(response){
//                    var data=response.data;
//                    $scope.isLoading=false;
//                    params.total(data[1][0].records);
//                    $defer.resolve(data[0]);
//                    
//                    $scope.adata = data[0];
//                });       
//            }
//        }
//
//    );
//    
//    $scope.updateParentGrid = function(){       
//        if($stateParams.routeId){
//            parentData = $scope.$parent.tableParams.data;           
//            for(var i=0;i<parentData.length;i++)
//                if(parentData[i].id==$stateParams.routeId)
//                   parentData[i].$selected=true; 
//        }       
//    }
//    
//    $scope.updateGridState=function(data){
//         if($stateParams.selectedId && $stateParams.selectedId>0){              
//            for(var i=0;i<data.length;i++)
//                if(data[i].id==$stateParams.selectedId){
//                   data[i].$selected=true;
//                   $scope.userId = data[i].id;
//                }
//        }
//    }
//    
//    $scope.changeSelection = function(user) {       
//        var data = $scope.tableParams.data;        
//        for(var i=0;i<data.length;i++){
//            if(data[i].id!=user.id)
//                data[i].$selected=false;
//        }
//    }
    
    function grdRefresh(){        
        $scope.grid.api.refresh();
    }

    function grdEdit(){
        if($scope.userId)
           $state.go('users.edit',{userId:$scope.userId}); 
        else{
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder editar, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        }
    }

    function grdAdd(){
        $state.go('users.add');
    }

    function grdDelete(id){
        if (id) {
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar este usuario?'
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                $scope.loading = true;
                UsersDataService.delete({id: id})
                .success(function(data, status, headers, config) {
                    $scope.loading = false;
                    if(data.error){
                        var modalOptions = {
                        actionButtonText: 'Aceptar',
                        bodyText: data.error
                    };
                    ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
                    }else{
                       var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: '¡Registro eliminado con éxito!'
                        };
                        ModalService.showModal({templateUrl: modalInfoPath}, modalOptions).then(function (result) {
                            $scope.grid.api.refresh();
                        });
                    }                    
                    })
                .error(function(data, status, headers, config) {
                    $scope.loading = false;
                    var modalOptions = {
                        actionButtonText: 'Aceptar',
                        bodyText: 'Ocurrió un error al eliminar el registro.'
                    };
                    ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
                });
            });
        }else{
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder eliminar, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        }
    }
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    $scope.customFilter = [ 
        {name:'Clave',type:'text',label:'Clave'},
        {name:'Nombre',type:'text',label:'Nombre'},
        {name:'Apellidos',type:'text',label:'Apellidos'},
        {name:'Usuario',type:'text',label:'Usuario'} 
    ];

    $scope.filterOpen = false;
    
    $scope.openFilter = function()
    {
        $scope.filterOpen = true;  
    };

    $scope.appFilter = function(filter)
    {
         var buscar={};
        var count = 0;
        
        //asigna los valores al arreglo de los parametros que se van buscar
        for(var i=0;i<$scope.customFilter.length;i++)
        {
            buscar[$scope.customFilter[i].name] = ($scope.customFilter[i].value) ? $scope.customFilter[i].value : null;
        }
        
        //cuenta los campos que son nullos
        for(var i=0;i<$scope.customFilter.length;i++)
        {
            if(buscar[$scope.customFilter[i].name] === null)
            {
                count++;
            }
        }
        
        //si todos los campos son nullos el arreglo se vacia
        if(count === $scope.customFilter.length)
        {
            buscar = null;
        }         
         $("#grdUsers").setGridParam({datatype: 'json', postData: { filtro : buscar} , page : 1}).trigger('reloadGrid');
    };
}
