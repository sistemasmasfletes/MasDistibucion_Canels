function PointsIndexController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,PointsDataService,UtilsService,CONFIG){
    
    var modalPath = PARTIALPATH.modal
    $scope.partials = PARTIALPATH.base;
    $scope.grid = {};
    $scope.isLoading=false;
    $scope.grid.edit = function(){ idbranche = ""; return grdEdit($scope.id)};
    $scope.editinline = function(schedule){
        var $id = schedule.id;
    	return grdEdit($id);
    };
    $scope.grid.delete=function(){grdDelete($scope.id, $scope.schedule)};
    $scope.partials = CONFIG.PARTIALS;
    $scope.postD = "";
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                code:'desc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = {page:params.page(), rowsPerPage:params.count(), srch:$scope.postD};
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                PointsDataService.getPoints(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);
                });       
            }
        }

    );
        
    $scope.changeSelection = function(schedule) {
        var data = $scope.tableParams.data;
        for(var i=0;i<data.length;i++){
            if(data[i].id!=schedule.id)
                data[i].$selected=false;
        }
        $scope.id = schedule.id;
        $scope.schedule = schedule;
        
    }

    $scope.search = function(data){
    	$scope.postD = data
    	$scope.tableParams.reload();
    }
    
    $scope.add = function(){
        $state.go('points.add');        idbranche = "";

    }
    
    function grdEdit(id){
        if(id){
            $state.go('points.edit',{pointId:id})
        } else {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para editar es necesario primero seleccionar un registro'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result){});
        }
    }
    
    function grdDelete(id,schedule){
        if(id){
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar este Punto de Venta?'
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                var pointId = schedule.id;
                if(id==pointId){
                    PointsDataService.delete(schedule);
                }
                $scope.tableParams.reload();
            });
        } else {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para eliminar es necesario primero seleccionar un registro'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result){});
        }
    }
    
    $scope.contact = function(schedule){
        var $id = schedule.id;
        $state.go('points.contact',{pointId:$id});
    }
    
    $scope.classification = function(schedule){
        var $id = schedule.id;
        $state.go('points.classification',{pointId:$id});
    }
    
    $scope.qrcode = function(schedule){
        var name = schedule.name;
        var image = schedule.code;
        var postParams = {id:schedule.id};
        PointsDataService.generatePDF(postParams);
        
        function getDataUri(url, callback) {
            var image = new Image();

            image.onload = function () {
                var canvas = document.createElement('canvas');
                canvas.width = this.naturalWidth;
                canvas.height = this.naturalHeight;

                canvas.getContext('2d').drawImage(this, 0, 0);

                // Get raw image data
                //callback(canvas.toDataURL('image/png').replace(/^data:image\/(png|jpg);base64,/, ''));
                // DataURI
                callback(canvas.toDataURL('image/png'));
            };
            setTimeout(function(){
                image.src = url;
            }, 1000);
            
        }
        
        getDataUri("../qrcode/"+image+".png", function(dataUri) {
            var getLayout = function () {
                return {
                    hLineWidth: function () {
                        return 0.1;
                    },
                    vLineWidth: function () {
                        return 0.1;
                    },
                    hLineColor: function () {
                        return 'gray';
                    },
                    vLineColor: function () {
                        return 'gray';
                    }
                };
            };
            
            var getImage = function() {
                return [
                    { text: 'Punto de venta: ' +name, fontSize: 20, bold: false, alignment: 'center', style: ['lineSpacing', 'headingColor'] },
                    { canvas: [{ type: 'line', x1: 0, y1: 5, x2: 595 - 2 * 40, y2: 5, lineWidth: 1, lineColor: '#8B1616', style: ['lineSpacing'] }] },
                    { text:'',style:['doublelineSpacing'] },
                    { image: dataUri, layout: getLayout(), alignment: 'center' },
                    { text: image, alignment: 'center'}
                ];
            };
            
            var docDefinition = {
                    pageMargins: [72, 80, 40, 60],
                    layout: 'headerLineOnly',
                    pageSize: 'A4',
                    header: function() {
                        return {
                            columns: [
                                {
                                    text:'Mas Distribucion' ,
                                    width: 200,
                                    margin: [50, 20, 5, 5]
                                }
                            ]
                        }
                    },

                    footer: function (currentPage, pageCount) {
                        return {
                            stack: [{ canvas: [{ type: 'line', x1: 0, y1: 5, x2: 595, y2: 5, lineWidth: 1, lineColor: '#8B1616', style: ['lineSpacing'] }] },
                            { text: '', margin: [0, 0, 0, 5] },
                            {
                                columns: [
                                    {},
                                    { text: currentPage.toString(), alignment: 'center' },
                                    { text: moment(new Date()).format("DD-MMM-YYYY"), alignment: 'right', margin: [0, 0, 20, 0] }
                                ]
                            }]

                        };
                    },
                content: [
                    {stack:getImage()}
                ],
                styles: {
                    'lineSpacing': {
                        margin: [0, 0, 0, 6]
                    },
                    'doublelineSpacing': {
                        margin: [0, 0, 0, 12]
                    },
                    'headingColor':
                    {
                        color: '#999966'
                    },
                    tableHeader: {
                        bold: true,
                        fontSize: 13,
                        color: '#669999'
                    }
                }
            }
            
            pdfMake.createPdf(docDefinition).download('QRCode_'+image+'.pdf');
        });
    };
}