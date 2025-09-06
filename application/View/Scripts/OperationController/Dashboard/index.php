
<div class="container">    
    <div><h1>Bienvenido</h1> </div>
    <div class="row" >
        <div class="blockGray">
            <div class="blockInner">
                <h2>Dashboard</h2>
                <br><br>
                
            </div>
        </div>        
    </div>    
   
    <!--  Definición de vistas parciales -->
    <script type="text/ng-template" id="index.html">
        <alert ng-repeat="alert in alerts" type="{{alert.type}}" close="closeAlert($index)">{{alert.msg}}</alert>
        <progressbar class="progress-striped active" value="100" type="info" ng-show="loading"></progressbar>
            <div class="toolbar">
                <div class="col-md-7">
                    &nbsp;
                </div>
                <div class="col-md-5 ">
                    <div class="pull-right">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group  btn-group-sm">
                                <button class="btn btn-default" ng-click="go('/index')"><span class="glyphicon glyphicon-home"></span> Inicio</button>
                                <button class="btn btn-default" ng-click="go('/edit/0')"><span class="glyphicon glyphicon-plus-sign"></span> Nuevo</button>
                                <button class="btn btn-default" ng-click="goEdit('/edit/'+vehicleId)"><span class="glyphicon glyphicon-edit"></span> Editar</button>
                                <button class="btn btn-default" ng-click="delete(userId)"><span class="glyphicon glyphicon-remove"></span> Eliminar</button>
                                <button class="btn btn-default" ng-click="refresh()"><span class="glyphicon glyphicon-refresh"></span> Actualizar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        <br>
        <ng-jqgrid vapi="apicall" config="config" api="gridapi" gridid="grdUsers01" pagerid="pager01"></ng-jqgrid>
    </script>
    
    <script type="text/ng-template" id="edit.html">
    </script>
    
    <script type="text/ng-template" id="modal.html">
        <!-- <div class="modal-header">
            <h3>{{modalOptions.headerText}}</h3>
        </div>-->
        <div class="modal-body">
            <br/>
            <p>{{modalOptions.bodyText}}</p>
            <br/>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" 
                    data-ng-click="modalOptions.close()">{{modalOptions.closeButtonText}}</button>
            <button type="button" class="btn btn-primary" 
                    data-ng-click="modalOptions.ok();">{{modalOptions.actionButtonText}}</button>
        </div>
    </script>
    
    <script type="text/ng-template" id="modalInfo.html">
        <!--<div class="modal-header">
            <h3>{{modalOptions.headerText}}</h3>
        </div>-->
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