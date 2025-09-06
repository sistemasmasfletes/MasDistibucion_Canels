<?php    
    $view->getJsManager()->loadJsFile('angularjs-1.2.18/angular.min.js');
    $view->getJsManager()->loadJsFile('angularjs-1.2.18/ui-bootstrap-tpls-0.11.0.js');
    $view->getJsManager()->loadJsFile('app/services/ModalService.js');
    $view->getJsManager()->loadJsFile('app/modules/customSelect/customSelect.js');
    $credentials = Model3_Auth::getCredentials();

/*    estilo responsive
/*    @media (max-width: 750px) {
    	.container {
    		/* si es muy angosta, uno debajo del otro */
/*    		flex-flow: column;
    	}
    }    
    
*/    
?>

<style>
<!--

@media (max-width: 750px) {
	.blockInner {
    /* si es muy angosta, uno debajo del otro */
		flex-flow: column;
    }
}  
-->
</style>
<div class="contiene-bread">
    <ol class="breadcrumb">
        <li class="active">Negocios</li>
        <li class="active "> <?php
            echo '<a href="'.$view->linkTo("/User/Store").'" >Tiendas</a>';
      	?></li>
        <li class="active actualpg "><?php echo $view->category->getName();?></li>
    </ol>
</div>
<div class="container" ng-app="promoApp" style=" width:100%; ">
    <div class="row" ng-controller="MainController" >
        <!--<div class="span12 blockGray">-->
            <div class="blockInner" style="display: flex;flex-flow: row; width:90%; margin:auto;">               
                <?php
                echo '<div style="width:52%; margin-right:5em;" >';
                echo '<h1><img style="" src="'.$view->getBaseUrl().'/' . $view->category->getImagePath().'" class="icono-categoria"/>'.$view->category->getName().'</h1>';
                echo '<br/><label for="kwd_search">Buscar:</label> <input type="text" id="kwd_search" value=""/><table id="my-table" class="table table-inverse" ><tbody id="myTable">';
                
                if(count($view->category->getUsers())>0){
                    echo '<tr class="ui-row-ltr"><td class="tabla-pers">';
                    echo '<td class="tabla-pers" colspan="6" style="color: blue; font-size:15px;">COMERCIOS REGISTRADOS </td>';
                    echo'</tr>';
                }
                
                foreach ($view->category->getUsers() as $user)
                {
                    if($view->userId!==$user->getId() 
                            && $user->getStatus() == DefaultDb_Entities_User::STATUS_ACTIVE 
                            && count($user->getBranches()))
                    {
                        $commercialName = "";
                        if($user->getCommercialName() != ""){
                            $commercialName = $user->getCommercialName();
                        } else {
                            $commercialName = $user->getFirstName(). ' ' . $user->getLastName();
                        }
                        echo '<tr class="ui-row-ltr"><td class="tabla-pers">';
                        if($user->getVisible() != '0'){
                            echo '<td class="tabla-pers"><a onclick="fiscalDat('.$user->getId().',1)" href="#" title="Informaci&oacute;n" ><span style="width:18px;height:18px" class="pers-btn icono-ver-actividad tam-normal"></span></a></td>';
                        }else{echo '<td class="tabla-pers"></td>'; }
                        echo '<td class="tabla-pers">'.$commercialName.'</td>';
                        echo '<td class="tabla-pers">';
                        $i=1;
                        foreach ($user->getBranches() as $branch){
                        	if($branch->getPoint() != NULL){
                        	if($branch->getPoint()->getUrlGoogleMaps() != ""){
                                echo '<a href="';
                                echo $branch->getPoint()->getUrlGoogleMaps();
                                echo '" target="_blank" title="Ubicaci&oacute;n '.$i.'">';
                                echo '<span class="pers-btn icono-posicion tam-normal"></span>';
                                echo '</a>';
                                $i++;
                            }}
                        }
                        echo '</td>';
                        echo '<td class="tabla-pers"><a style="color:green;" onclick="goToPage('.$user->getId().',null,true)" title="Ver cat&aacute;logo">';
                        echo 'Ver Cat&aacute;logos';
                        echo '</a></td>';
                        echo '<td class="tabla-pers"><a class=" pull-right" title="Ir a carrito" href="'.$view->url(array('controller' => 'Store', 'action' => 'viewCart', 'id' => $user->getId())).'">';
                        echo '<span class="pers-btn icono-carrito tam-normal"></span>';
                        echo '</a></td>';
                        echo '<td class="tabla-pers"><a title="Enviar promoci&oacute;n" ng-click="schedulePromotion('.$user->getId().',null,true)" href="#"><span style="width:18px;height:18px" class="pers-btn icono-calendario tam-normal"></span></a></td>';
                        echo'</tr>';
                    }
                }
                
                
                echo '<tbody></table>';
                echo '<ul class="pagination pagination-lg pager" id="myPager"></ul>';
                

                echo '<br/><label for="kwd_search1">Buscar:</label><input type="text" id="kwd_search1" value=""/><table id="my-table1" class="table table-inverse" ><tbody id="myTable1">';
                echo '<tr class="ui-row-ltr"><td class="tabla-pers">';
                echo '<td class="tabla-pers" colspan="6" style="color: blue; font-size:15px;">COMERCIOS NO REGISTRADOS</td>';
                echo'</tr>';
                
                foreach ($view->allstores as $point)
                {
                    $punto = array("id"=>$point["id"],"nameAddress"=>$point["name"].' ('.$point["address"].')');
                    echo '<tr class="ui-row-ltr"><td class="tabla-pers">';
                    echo '<td class="tabla-pers">';
                    echo '<a onclick="fiscalDat('.$point["id"].',2)" href="#" ><span style="width:18px;height:18px" class="pers-btn icono-ver-actividad tam-normal"></span></a>';
                    echo '</td>';
                    echo '<td class="tabla-pers">'.$point["name"].'</td>';
                    echo '<td class="tabla-pers">';
                    if($point["urlGoogleMaps"] != ""){
                        echo '<a href="'.$point["urlGoogleMaps"].'" target="_blank" title="Ubicaci&oacute;n"><span class="pers-btn icono-posicion tam-normal"></span></a>';
                    }
                    echo '</td>';
                    echo '<td class="tabla-pers">';
                    echo '</td>';
                    echo '<td class="tabla-pers">';
                    echo '</td>';
                    echo '<td class="tabla-pers"><a ng-click="schedulePromotion(null,'.htmlspecialchars(json_encode($punto)).',false)" href="#"><span style="width:18px;height:18px" class="pers-btn icono-calendario tam-normal"></span></a></td>';
                    echo'</tr>';
                }
                
                echo '<tbody></table>';
                echo '<ul class="pagination pagination-lg pager" id="myPager1"></ul>';
                echo '</div>';
                echo '<div id="user" ; !important" style="width:45%"></div>'
                ?>
            </div>
        <!--</div>-->
    </div>

    <!-- Plantillas Angular -->
    <script type="text/ng-template" id="templatePromotion.html">
        <div class="modal-body">
            <div class="panel-heading">Programar envío de Promoción</div>
            <form role="form" novalidate name="sc.frmRoute">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="promotionid">Promoción</label>
                        <input type="hidden" class="form-control" ng-model="schedule.id">
                        <select ng-model="select.selPromotion"
                                    ng-options="option.name for option in promotions track by option.id"
                                    class="form-control"
                                    id="promotionid"
                                    name="promotionid"
                                    ng-change="onPromoSelect()">
                                <option value="">Seleccionar promoción</option>
                            </select>
                    </div>
                    <div class="form-group" ng-class="">
                        <label for="branchid">Sucursal</label>
                        <select id="branchid" name="branchid" class="form-control" ng-model="promotionSchedule.branchid" ng-change="calculateDelivery()"
                                ng-options="opt.id as opt.nameAddress for opt in branchesUser">
                            <option value="" >Selecciona una sucursal</option>
                        </select>
                    </div>
                    <div class="form-group" ng-class="">
                        <label for="promotionDate">Fecha Promoción</label>
                        <select id="promotionDate" name="promotionDate" class="form-control" ng-model="promotionSchedule.promotionDate"
                                ng-options="label for label in deliveryDates">
                            <option value="" >Selecciona una fecha de entrega</option>
                        </select>
                    </div>
                    <div class="form-group pull-right">
                        <label for="credit">Créditos disponibles</label>
                        <input type="text" class="form-control" id="credit" ng-disabled="true" name="credit" placeholder="" ng-model="creditos">                      
                    </div>
                </div>
                <br/>
                <div class="col-md-1" style="border-top:1px dotted #ccc;clear:both">
                    <span style="font-size:200; font-weight:bold">Costo promoción: {{totalCost|currency : ''}} crédito(s)</span>
                </div>
                <br/>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-info" data-ng-click="ok();">Cerrar</button>
            <button type="button" class="btn btn-primary" data-ng-click="savePromotionSchedule();">Guardar</button>
        </div>
    </script>

    <script type="text/ng-template" id="templateMessagebox.html">
        <div class="modal-body">
        <br/>
        <p>{{modalOptions.bodyText}}</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary"
                    data-ng-click="modalOptions.ok();">{{modalOptions.actionButtonText}}</button>
        </div>
    </script>
</div>
<script type="text/javascript">

/****************************PAGINADO PARA TABLAS HTML**********************************************/                
	$.fn.pageMe = function(opts){
	    var $this = this,
	        defaults = {
	            perPage: 7,
	            showPrevNext: false,
	            hidePageNumbers: false
	        },
	        settings = $.extend(defaults, opts);
	    
	    var listElement = $this;
	    var perPage = settings.perPage; 
	    var children = listElement.children();
	    var pager = $('.pager');
	    
	    if (typeof settings.childSelector!="undefined") {
	        children = listElement.find(settings.childSelector);
	    }
	    
	    if (typeof settings.pagerSelector!="undefined") {
	        pager = $(settings.pagerSelector);
	    }
	    
	    var numItems = children.size();
	    var numPages = Math.ceil(numItems/perPage);
	
	    pager.data("curr",0);
	    
	    if (settings.showPrevNext){
	        $('<li><a href="#" class="prev_link"> < </a></li>').appendTo(pager);
	    }
	    
	    var curr = 0;
	    while(numPages > curr && (settings.hidePageNumbers==false)){
	        $('<li><a href="#" class="page_link">'+(curr+1)+'</a></li>').appendTo(pager);
	        curr++;
	    }
	    
	    if (settings.showPrevNext){
	        $('<li><a href="#" class="next_link"> > </a></li>').appendTo(pager);
	    }
	    
	    pager.find('.page_link:first').addClass('active');
	    pager.find('.prev_link').hide();
	    if (numPages<=1) {
	        pager.find('.next_link').hide();
	    }
	  	pager.children().eq(1).addClass("active");
	    
	    children.hide();
	    children.slice(0, perPage).show();
	    
	    pager.find('li .page_link').click(function(){
	        var clickedPage = $(this).html().valueOf()-1;
	        goTo(clickedPage,perPage);
	        return false;
	    });
	    pager.find('li .prev_link').click(function(){
	        previous();
	        return false;
	    });
	    pager.find('li .next_link').click(function(){
	        next();
	        return false;
	    });
	    
	    function previous(){
	        var goToPage = parseInt(pager.data("curr")) - 1;
	        goTo(goToPage);
	    }
	     
	    function next(){
	        goToPage = parseInt(pager.data("curr")) + 1;
	        goTo(goToPage);
	    }
	    
	    function goTo(page){
	        var startAt = page * perPage,
	            endOn = startAt + perPage;
	        
	        children.css('display','none').slice(startAt, endOn).show();
	        
	        if (page>=1) {
	            pager.find('.prev_link').show();
	        }
	        else {
	            pager.find('.prev_link').hide();
	        }
	        
	        if (page<(numPages-1)) {
	            pager.find('.next_link').show();
	        }
	        else {
	            pager.find('.next_link').hide();
	        }
	        
	        pager.data("curr",page);
	      	pager.children().removeClass("active");
	        pager.children().eq(page+1).addClass("active");
	    
	    }
	    $(".pager a").css('padding',"3px 4px")
	};
/****************************PAGINADO PARA TABLAS HTML**********************************************/                
                
    //Código GlobalSoft
    //===============================================================================
    function goToPage(user) {
             $.ajax({url: '<?php echo $view->getBaseUrl(); ?>/User/Store/user/id/'+user+'/',
            data: {action: 'test', arguments: '1'},
            type: 'post',
            success: function (output) {
                var nodo = output;
                var buscar = String('<div class="blockInner">');
                var lonBuscar = buscar.length;
                var pos = nodo.search(buscar);
                var sc = nodo.substring(pos + lonBuscar, nodo.length);
                 document.getElementById("user").innerHTML = sc;
            }
        });
    }

    function addToFavoriteBuyer(arg)
    {
        var arrId = arg.split('_');
        var typeFavorite = document.getElementById('typeFavoriteBuyer').value;
        var clientId = arrId[3];
        
        $.post(urlAddFavorite, {'clientId':clientId,
                                'type':typeFavorite}, function(data)
        {
            if(data == true || data == 'true')
            {
                $('#addFavoriteBuyer').hide();
                $('#removeFavoriteBuyer').show();
                alert('Ha agregado al cliente a sus favoritos');
            }
        });
    }

    function removeFromFavoriteBuyer(arg)
    {
        var arrId = arg.split('_');
        var typeFavorite = document.getElementById('typeFavoriteBuyer').value;
        var clientId = arrId[2];
        
        $.post(urlRemoveFavorite, {'clientId':clientId,
                                    'type':typeFavorite}, function(data)
        {
            if(data == true || data == 'true')
            {
                $('#addFavoriteBuyer').show();
                $('#removeFavoriteBuyer').hide();
                alert('Ha eliminado al cliente a sus favoritos');
            }
        });
    }

    function addToFavoriteSeller(arg)
    {
        var arrId = arg.split('_');
        var typeFavorite = document.getElementById('typeFavoriteSeller').value;
        var clientId = arrId[3];
        
        $.post(urlAddFavorite, {'clientId':clientId,
                                'type':typeFavorite}, function(data)
        {
            if(data == true || data == 'true')
            {
                $('#addFavoriteSeller').hide();
                $('#removeFavoriteSeller').show();
                alert('Ha agregado al proveedor a sus favoritos');
            }
        });
    }

    function removeFromFavoriteSeller(arg)
    {
        var arrId = arg.split('_');
        var typeFavorite = document.getElementById('typeFavoriteSeller').value;
        var clientId = arrId[2];
        
        $.post(urlRemoveFavorite, {'clientId':clientId,
                                    'type':typeFavorite}, function(data)
        {
            if(data == true || data == 'true')
            {
                $('#addFavoriteSeller').show();
                $('#removeFavoriteSeller').hide();
                alert('Ha eliminado al proveedor a sus favoritos');
            }
        });
    }

    var currentUser = <?php echo $credentials["id"];?>

    //Aplicación de programación de promociones
    //===============================================================================
    var promoApp = angular.module("promoApp",['ui.bootstrap','masDistribucion.customSelect']);

    promoApp
    .service('ModalService',['$modal',ModalService])
    .service('PromotionScheduleDataService',['$http',PromotionScheduleDataService])


    function MainController($scope,$http,ModalService){       

        $scope.schedulePromotion = function(clientId,point,isRegistered){
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: ''
            };
            ModalService.showModal(
                {   templateUrl: 'templatePromotion.html',
                    controller:'PromoController',size:'xl',
                    resolve:{
                        promotiondata:function(){
                            return {user: currentUser, client:clientId, point:point, isRegistered:isRegistered}
                        }
                    }
                },
                modalOptions).then(function (result) {});
        }

    }

    function PromoController($scope,$http,$filter,ModalService,$modalInstance,PromotionScheduleDataService,promotiondata){   
        $scope.promotiondata=promotiondata;
        $scope.promotionSchedule = {userid:promotiondata.user, clientid:promotiondata.client, promotionid:null};
        $scope.branchesUser = [];
        $scope.deliveryDates = [];
        $scope.promotions=[];
        $scope.select = {};
        
        $scope.onPromoSelect = function(){
            $scope.promotionSchedule.promotionid = ($scope.select.selPromotion) ? $scope.select.selPromotion.id : null;
            var arrPromoData = PromotionScheduleDataService.getData('promotions');
            var promotionCosting = arrPromoData[1].promotionCosting;
            $scope.totalCost = ($scope.select.selPromotion) ? (promotionCosting * $scope.select.selPromotion.numResources):null;
        }

        $scope.ok = function (result) {
                $modalInstance.close($scope.promotiondata);
            };
       
        $scope.calculateDelivery=function(){
            if(!$scope.promotionSchedule.branchid){
                $scope.deliveryDates=[];
                $scope.promotionSchedule.promotionDate=null;
                return;
            }
            PromotionScheduleDataService.calculatePromotionDelivery({branchid:$scope.promotionSchedule.branchid, isRegistered:promotiondata.isRegistered}).then(function(response){
                $scope.deliveryDates = response.data.dates;
                $scope.promotionSchedule.routepointid = response.data.routepointid;
                $scope.promotionSchedule.promotionDate=null;
            })
        }

        $scope.savePromotionSchedule = function(){        
            if($scope.promotionSchedule.promotionid==null || $scope.promotionSchedule.promotionDate==null){
                var modalOptions = {
                    actionButtonText: 'Aceptar',
                    bodyText: "Necesita capturar todos los campos."
                };
                ModalService.showModal(
                    {templateUrl: 'templateMessagebox.html',size:'sm'},
                    modalOptions).then(function (result) {});
                return;
            }
            $scope.promotionSchedule.totalCost = $scope.totalCost;
            PromotionScheduleDataService.savePromotionSchedule($scope.promotionSchedule).then(function(response){
                var result = response.data; 
                var modalOptions = {
                    actionButtonText: 'Aceptar',
                    bodyText: result.message
                };
                ModalService.showModal(
                    {templateUrl: 'templateMessagebox.html',size:'sm'},
                    modalOptions).then(function (result) {
                        $modalInstance.close();
                    });
            });
        }

        //Obtener sucursales del usuario si es cliente registrado
        if(promotiondata.isRegistered){
            PromotionScheduleDataService.getBranchesUser({clientid:promotiondata.client}).then(function(response){            
                $scope.branchesUser = response.data;
            });
        }else{
            $scope.branchesUser = [promotiondata.point];
        }
        
        //Obtener promociones del usuario, desde el caché local o desde el servidor
        if(PromotionScheduleDataService.getData('promotions').length==0){        
            PromotionScheduleDataService.getPromotion({filter:{name:""}}).then(function(response){
                var arrData = response.data.data;
                $scope.promotions = arrData;
                PromotionScheduleDataService.setData([arrData,response.data.meta],'promotions');
            });
        }else{
            $scope.promotions =  PromotionScheduleDataService.getData('promotions')[0];
        }

        //Obtener créditos
        PromotionScheduleDataService.getCredit().then(function(response){
            $scope.creditos=$filter('currency')(response.data.creditos,'');
        })
    }

    
    //Servicios
    //===============================================================================
    function PromotionScheduleDataService($http){
        var branchesUser = [];
        var promotions = [];

        this.setData=function(externalData,arrayName){
            switch(arrayName){
                case 'branchesUser':
                    while(branchesUser.length > 0) {
                        branchesUser.pop();
                    }
                    branchesUser = externalData.slice();
                    break;
                case 'promotions':
                    while(promotions.length > 0) {
                        promotions.pop();
                    }
                    promotions = externalData.slice();
                    break;
            }            
        }

        this.getData=function(arrayName){
            switch(arrayName){
                case 'branchesUser': return branchesUser;break;
                case 'promotions': return promotions;break;
            }
        }

        this.getBranchesUser=function(paramsData,httpParams){
            return $http.post(urlGetBranchesUser, paramsData, httpParams)
        }

        this.getPromotion=function(paramsData,httpParams){
            var defaultParams = {page: 1, rowsPerPage: 10, sortField: '', sortDir: ''};
            if(!paramsData)
                paramsData = defaultParams

            var params = angular.extend({},defaultParams,paramsData);
            return $http.post(urlGetPromotion, paramsData, httpParams)
        }

        this.calculatePromotionDelivery=function(paramsData,httpParams){
            return $http.post(urlCalculatePromotionDelivery, paramsData, httpParams)
        }

        this.savePromotionSchedule = function(paramsData,httpParams){
            return $http.post(urlSavePromotionSchedule, paramsData, httpParams)
        }

        this.getCredit = function(){
            return $http.post(urlGetCredit, {}, {})
        }
    }

    function fiscalDat(id,op) {/////FUNCION PARA OBTENER LOS DATOS DE LOS PUNTOS EN EL LISTADO DE TIENDAS//////////////////// 
        $.ajax({url: '<?php echo $view->getBaseUrl(); ?>/User/Store/fiscalDat/',
            data: {id: id, op: op},
            type: 'post',
            success: function (output) {
                var nodo = output;
                var buscar = String('INFOI');
                var buscar2 = String('INFOF');
                var lonBuscar = buscar.length;
                var posI = nodo.search(buscar);
                var posF = nodo.search(buscar2);
                var sc = nodo.substring(posI + lonBuscar, posF - lonBuscar);
                document.getElementById("user").innerHTML = sc;
           }
        });
    }

    $(document).ready(function(){
        
    	  $('#myTable').pageMe({pagerSelector:'#myPager',showPrevNext:true,hidePageNumbers:false,perPage:6});
    	  $('#myTable1').pageMe({pagerSelector:'#myPager1',showPrevNext:true,hidePageNumbers:false,perPage:6});

    		$("#kwd_search").keyup(function(){
    			if( $(this).val() != ""){
    				$("#myTable>tr").hide();
    				$("#myTable td:contains-ci('" + $(this).val() + "')").parent("tr").show();
    			}else{
    				// When there is no input or clean again, show everything back
    				$("#myTable>tr").show();
    			}
    		});

    		$("#kwd_search1").keyup(function(){
    			// When value of the input is not blank
    			if( $(this).val() != ""){
    				// Show only matching TR, hide rest of them
    				$("#myTable1>tr").hide();
    				$("#myTable1 td:contains-ci('" + $(this).val() + "')").parent("tr").show();
    			}else{
    				// When there is no input or clean again, show everything back
    				$("#myTable>tr").show();
    			}
    		});
    	var idComercioSeleccionado = <?php echo isset($_SESSION["idcomercio"]) ? $_SESSION["idcomercio"] : "null";?>;
        if(idComercioSeleccionado)
            goToPage(idComercioSeleccionado);
    });
    // jQuery expression for case-insensitive filter
    $.extend($.expr[":"], 
    {
        "contains-ci": function(elem, i, match, array) 
    	{
    		return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    	}
    });
    
</script>
<?php 
    if(isset($_SESSION["idcomercio"]))
        unset($_SESSION["idcomercio"]);
?>