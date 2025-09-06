<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" ng-app="masDistribucion">
    <head>
        <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <!-- meta name="viewport" content="width=device-width, initial-scale=1"-->
                    <link rel="shortcut icon" href="<?php echo $view->getBaseUrlPublic(); ?>/images/ui/favicon.png" />
                    <!-- librerías opcionales que activan el soporte de HTML5 para IE8 -->
                    <!--[if lt IE 9]>
                      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
                      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
                    <![endif]-->        
                    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" />
                    <title>Mas distribuci&oacute;n</title>
                    </head>

                    <body>
                        <div zd-login is-open="false">        
                            <div zd-sys-messaging>
                                <div id="menu">
                                    <?php $view->MenuHelper()->display(); ?>
                                </div>
                                <div style="background: #FFF; padding-bottom: 0px; padding-top: 0px;" >
                                    <div class="rootcontainer">
                                        <div class="contiene-bread">
                                        </div>
                                        <div class="container"> 
                                            <div ui-view="main">

                                            </div>
                                        </div>
                                    </div>

                                    <!--<div class="row" >-->
                                    <!--<div class=" blockGray">-->
                                    <!--<div class="blockInner">-->     

                                    <!--</div>-->
                                    <!--</div>-->
                                    <!--</div>-->

                                </div>    
                                <div id="footer">
                                    <p align="center" valign="middle">&copy; Mas Fletes.com, Servicio de Fletes econ&oacute;micos en San Luis Potos&iacute;, M&eacute;xico<br/></p>
                                </div>

                                <?php
                                if(isset($_SESSION['isapp'])){
                                ?>
	                                <style>
									body {
										background-image: none !important;
										background-color: #fff !important;
									}
									</style>
                                <?php
                                }
                                
                                $view->getCssManager()->loadCssFile('bootstrap-3.2.0/bootstrap.min.css');
                                $view->getCssManager()->loadCssFile('custom.css');
                                $view->getCssManager()->loadCssFile('smartmenu/jquery.smartmenus.bootstrap.css');

                                $view->getCssManager()->loadCssFile('jqgrid/blitzer/jquery-ui-1.10.3.custom.css');
                                $view->getCssManager()->loadCssFile('jqgrid/ui.jqgrid.css');
                                $view->getCssManager()->loadCssFile('jqgrid/jqGrid.overrides.css');
                                $view->getCssManager()->loadCssFile('application/GeneralContentLayout.css');
                                $view->getCssManager()->loadCssFile('ng-table/ng-table.css');

                                $view->getJsManager()->loadJsFile('jquery/jquery-1.9.1.min.js');

                                $view->getJsManager()->loadJsFile('smartmenu/jquery.smartmenus.min.js');
                                $view->getJsManager()->loadJsFile('smartmenu/jquery.smartmenus.bootstrap.min.js');

                                $view->getJsManager()->loadJsFile('angularjs-1.2.18/angular.min.js');
                                $view->getJsManager()->loadJsFile('angularjs-1.2.18/angular-ui-router.min.js');
                                $view->getJsManager()->loadJsFile('angularjs-1.2.18/angular-cookies.min.js');
                                $view->getJsManager()->loadJsFile('angularjs-1.2.18/webcam.js');
                                $view->getJsManager()->loadJsFile('angularjs-1.2.18/ui-bootstrap-tpls-0.11.0.js');

                                $view->getJsManager()->loadJsFile('jquery/jquery-migrate-1.2.1.min.js');
                                $view->getJsManager()->loadJsFile('bootstrap-3.2.0/bootstrap.min.js');
                                $view->getJsManager()->loadJsFile('jquery/jquery-ui-1.10.3.custom.min.js');
                                $view->getJsManager()->loadJsFile('jqgrid/i18n/grid.locale-es.js');
                                $view->getJsManager()->loadJsFile('jqgrid/jquery.jqGrid.src.js');
                                $view->getJsManager()->loadJsFile('dist/qr-scanner.js');
                                $view->getJsManager()->loadJsFile('dist/jsqrcode-combined.min.js');
                                $view->getJsManager()->loadJsFile('snapshoot/webcam.js');
                                $view->getJsManager()->loadJsFile('snapshoot/webcam.min.js');
                                $view->getJsManager()->addJsVar('exitLink', json_encode($view->url(array('module' => false, 'controller' => 'Index', 'action' => 'logout'))));
                                $view->getJsManager()->loadJsFile('application/exit.js');
                                $view->getJsManager()->loadJsFile('http-auth-interceptor/http-auth-interceptor.js');

                                //$view->getJsManager()->loadJsFile('bootstrap-3.2.0/ui-bootstrap-tpls-0.14.2.min.js');
//                                $view->getJsManager()->loadJsFile('dist/quagga.js');
//                                $view->getJsManager()->loadJsFile('dist/quagga.min.js');
//                                $view->getJsManager()->loadJsFile('dist/html5-qrcode.min.js');

//                                $view->getJsManager()->loadJsFile('angularjs-1.2.18/qrcode.js');
//                                $view->getJsManager()->loadJsFile('angularjs-1.2.18/qrcode_utf8.js');
//                                $view->getJsManager()->loadJsFile('angularjs-1.2.18/angular-qrcode.js');
                                $view->getJsManager()->loadJsFile('dist/pdfmake.min.js');
                                $view->getJsManager()->loadJsFile('dist/vfs_fonts.js');
                                $view->getJsManager()->loadJsFile('dist/moment.js');


                                $view->getJsManager()->loadJsFile('ng-table/ng-table.js');
                                $view->getJsManager()->loadJsFile('ng-csv/ng-csv.js');
                                $view->getJsManager()->loadJsFile('ng-csv/angular-sanitize.js');

                                $view->getJsManager()->addJsVarEncode('path', $view->getJsManager()->getBaseUrl());
                                $view->getJsManager()->addJsVarEncode('pathJs', $view->getJsManager()->getBaseDir());
                                $view->getJsManager()->addJsVarEncode('pathCss', $view->getCssManager()->getBaseDir());
                                $view->getJsManager()->loadJs();

                                $view->getJsManager()->loadJsFile('app/factories/HttpInterceptor.js');
                                $view->getJsManager()->loadJsFile('app/services/UtilsService.js');
                                $view->getJsManager()->loadJsFile('app/services/ModalService.js');
                                $view->getJsManager()->loadJsFile('app/services/JQGridService.js');
                                $view->getJsManager()->loadJsFile('app/services/SessionData.js');
                                $view->getJsManager()->loadJsFile('app/services/CatalogService.js');
                                $view->getJsManager()->loadJsFile('app/services/DataService.js');
                                $view->getJsManager()->loadjsFile('app/services/CausesDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/RouteSummaryDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/ActivityTypeDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/TransactionTypeDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/RoutesDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/PointsDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/VehiclesDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/StatesDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/UsersDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/ScheduleDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/ActivityReportDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/ReportDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/InventoryDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/AuthService.js');
                                $view->getJsManager()->loadJsFile('app/services/BancosDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/TipoPagosDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/TipoMovimientosDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/EstatusDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/TipoMonedasDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/PaisesDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/CuentasDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/ConversionDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/CompraCreditosDataService.js');

                                $view->getJsManager()->loadjsFile('app/services/ContractDataService.js');

                                $view->getJsManager()->loadJsFile('app/services/AprobacionCreditosDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/TransferenciaCreditosDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/BalanceGeneralDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/PagosDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/ActividaChoferLogDataService.js');
                                $view->getJSManager()->loadJsFile('app/services/AprobacionCreditoControladorDataService.js');
                                $view->getJSManager()->loadJsFile('app/services/AdministracionLogClienteDataService.js');
                                $view->getJSManager()->loadJsFile('app/services/WarehousemanDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/ClientPackageDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/PackageRateDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/ConfigurationDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/AddressDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/PromotionDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/ZoneDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/CardOperatorsDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/EstadosDataService.js');
                                $view->getJsManager()->loadJsFile('app/services/CiudadesDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/InventoryWarehousemanDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/PromotionScheduleDataService.js');
                                $view->getJsManager()->loadjsFile('app/services/PromotionReceivedDataService.js');
//            $view->getJsManager()->loadJsFile('app/services/ActividadChoferDataService.js');
                                
                                $view->getJsManager()->loadJsFile('app/directives/ngJqgrid.js');
                                $view->getJsManager()->loadJsFile('app/directives/ZdMonthpicker.js');
                                $view->getJsManager()->loadJsFile('app/directives/LoadingContainer.js');
                                $view->getJsManager()->loadJsFile('app/directives/ZdFilter.js');
                                $view->getJsManager()->loadJsFile('app/directives/ZdLogin.js');
                                $view->getJsManager()->loadJsFile('app/directives/ZdSysMessaging.js');
                                $view->getJsManager()->loadJsFile('app/directives/ZdRating.js');

                                $view->getJsManager()->loadJsFile('app/controllers/InitController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/LoginController.js');
								
                                $view->getJsManager()->loadJsFile('app/controllers/routes/RoutesIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/routes/RoutesEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/routes/RoutesEditPointsController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/vehicles/VehiclesIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/vehicles/VehiclesEditController.js');
								
                               $view->getJsManager()->loadJsFile('app/controllers/causes/CausesIndexController.js');
                               $view->getJsManager()->loadJsFile('app/controllers/causes/CausesEditController.js');
                               $view->getJsManager()->loadJsFile('app/controllers/routeSummary/RouteSummaryIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/routeSummary/RouteSummaryEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/routeSummary/RouteSummaryEditPackageController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/routeSummary/RouteSummaryEditPointsController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/routeSummary/RouteSummaryEvidenceController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/routeSummary/ActivityPackageController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/routeSummary/PacksRouteController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/activityType/ActivityTypeIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/activityType/ActivityTypeEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/transactionType/TransactionTypeIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/transactionType/TransactionTypeEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/points/PointsIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/points/PointsEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/users/UsersIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/users/UsersEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/schedule/ScheduleIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/estados/EstadosIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/estados/EstadosEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/ciudades/CiudadesIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/ciudades/CiudadesEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/schedule/ScheduleEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/schedule/ScheduleDetailController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/schedule/ScheduledDatesController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/schedule/ScheduledDatesEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/scheduledRoute/ScheduledRouteIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/scheduledRoute/ScheduledRouteSchedulesController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/scheduledRoute/ScheduledRouteActivitiesController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/scheduledRoute/RoutePointActivitiesController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/scheduledRoute/srPackagesController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/report/ReportIndexController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/inventory/InventoryIndexController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/inventory/InventoryPacksController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/warehouseman/WarehousemanIndexController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/warehouseman/WarehousemanPointController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/warehouseman/WarehousemanPackController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/warehouseman/WarehousemanSaveTransferController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/warehouseman/WarehousemanPackageTrackingController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/warehouseman/WarehousemanActivityController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/warehouseman/WarehousemanRejectedController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/warehouseman/WarehousemanRejectededitController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/contracts/ContractsIndexController.js');
                                
                                $view->getJsManager()->loadjsFile('app/controllers/addresses/AddressIndexController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/addresses/AddressesEditController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/points/ContactIndexController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/points/ContactEditController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/points/ClassificationEditController.js');

                                $view->getJsManager()->loadJsFile('app/controllers/bancos/BancosIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/bancos/BancosEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/tipoPagos/TipoPagosIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/tipoPagos/TipoPagosEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/tipoMovimientos/TipoMovimientosIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/tipoMovimientos/TipoMovimientosEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/estatus/EstatusIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/estatus/EstatusEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/tipoMonedas/TipoMonedasIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/tipoMonedas/TipoMonedasEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/paises/PaisesIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/paises/PaisesEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/cuentas/CuentasIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/cuentas/CuentasEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/conversion/ConversionIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/conversion/ConversionEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/compraCreditos/CompraCreditosIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/compraCreditos/CompraCreditosEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/aprobacionCreditos/AprobacionCreditosIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/aprobacionCreditos/AprobacionCreditosEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/transferenciaCreditos/TransferenciaCreditosIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/transferenciaCreditos/TransferenciaCreditosEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/balanceGeneral/BalanceGeneralIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/pagos/PagosIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/actividaChoferLog/ActividaChoferLogIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/actividaChoferLog/ActividaChoferLogEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/aprobacionCreditoControlador/AprobacionCreditoControladorIndexController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/aprobacionCreditoControlador/AprobacionCreditoControladorEditController.js');
                                $view->getJsManager()->loadJsFile('app/controllers/administracionLogCliente/AdministracionLogClienteIndexController.js');
                                $view->getJsManager()->loadjsFile('app/controllers/inventoryWarehouseman/InventoryWarehousemanController.js');

//                                $view->getJsManager()->loadjsFile('app/controllers/inventoryWarehouseman/InventoryWarehousemanController.js');
//            $view->getJsManager()->loadJsFile('app/controllers/actividadesChofer/ActividadesChoferIndexController.js');

                                $view->getJsManager()->loadJsFile('app/app.dep.js');
                                $view->getJsManager()->loadJsFile('app/modules/routes/RoutesConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/routes/routes.js');
                                $view->getJsManager()->loadJsFile('app/modules/vehicles/VehiclesConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/vehicles/vehicles.js');
                                $view->getJsManager()->loadJsFile('app/modules/causes/CausesConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/causes/causes.js');
                                $view->getJsManager()->loadJsFile('app/modules/routeSummary/RouteSummaryConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/routeSummary/routeSummary.js');
                                $view->getJsManager()->loadJsFile('app/modules/activityType/ActivityTypeConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/activityType/activityType.js');
                                $view->getJsManager()->loadJsFile('app/modules/transactionType/TransactionTypeConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/transactionType/transactionType.js');
                                $view->getJsManager()->loadJsFile('app/modules/points/PointsConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/points/points.js');
                                $view->getJsManager()->loadJsFile('app/modules/users/UsersConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/users/users.js');
                                $view->getJsManager()->loadJsFile('app/modules/schedule/ScheduleConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/schedule/schedule.js');
                                $view->getJsManager()->loadJsFile('app/modules/scheduledRoute/ScheduledRouteConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/scheduledRoute/scheduledRoute.js');
                                $view->getJsManager()->loadJsFile('app/modules/report/ReportConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/report/report.js');
                                $view->getJsManager()->loadjsFile('app/modules/inventory/InventoryConfig.js');
                                $view->getJsManager()->loadjsFile('app/modules/inventory/inventory.js');
                                $view->getJsManager()->loadJsFile('app/modules/bancos/BancosConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/bancos/bancos.js');
                                $view->getJsManager()->loadJsFile('app/modules/tipoPagos/TipoPagosConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/tipoPagos/tipoPagos.js');
                                $view->getJsManager()->loadJsFile('app/modules/tipoMovimientos/TipoMovimientosConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/tipoMovimientos/tipoMovimientos.js');
                                $view->getJsManager()->loadJsFile('app/modules/estatus/EstatusConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/estatus/estatus.js');
                                $view->getJsManager()->loadJsFile('app/modules/tipoMonedas/TipoMonedasConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/tipoMonedas/tipoMonedas.js');
                                $view->getJsManager()->loadJsFile('app/modules/paises/PaisesConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/paises/paises.js');
                                $view->getJsManager()->loadJsFile('app/modules/cuentas/CuentasConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/cuentas/cuentas.js');
                                $view->getJsManager()->loadJsFile('app/modules/conversion/ConversionConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/conversion/conversion.js');
                                $view->getJsManager()->loadJsFile('app/modules/compraCreditos/CompraCreditosConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/compraCreditos/compraCreditos.js');

                                $view->getJsManager()->loadJsFile('app/modules/contracts/ContractConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/contracts/contract.js');

                                $view->getJsManager()->loadJsFile('app/modules/aprobacionCreditos/AprobacionCreditosConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/aprobacionCreditos/aprobacionCreditos.js');
                                $view->getJsManager()->loadJsFile('app/modules/transferenciaCreditos/TransferenciaCreditosConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/transferenciaCreditos/transferenciaCreditos.js');
                                $view->getJsManager()->loadJsFile('app/modules/balanceGeneral/BalanceGeneralConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/balanceGeneral/balanceGeneral.js');
                                $view->getJsManager()->loadJsFile('app/modules/pagos/PagosConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/pagos/pagos.js');
                                $view->getJsManager()->loadJsFile('app/modules/actividaChoferLog/ActividaChoferLogConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/actividaChoferLog/actividaChoferLog.js');
                                $view->getJsManager()->loadJsFile('app/modules/aprobacionCreditoControlador/AprobacionCreditoControladorConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/aprobacionCreditoControlador/aprobacionCreditoControlador.js');
                                $view->getJsManager()->loadJsFile('app/modules/administracionLogCliente/AdministracionLogClienteConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/administracionLogCliente/administracionLogCliente.js');
                                $view->getJsManager()->loadJsFile('app/modules/customSelect/customSelect.js');
                                $view->getJsManager()->loadJsFile('app/modules/warehouseman/WarehousemanConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/warehouseman/warehouseman.js');
                                $view->getJsManager()->loadjsFile('app/modules/clientPackage/clientPackage.js');
                                $view->getJsManager()->loadjsFile('app/modules/packageRate/packageRate.js');
                                $view->getJsManager()->loadjsFile('app/modules/configuration/configuration.js');
                                $view->getJsManager()->loadjsFile('app/modules/addresses/AddressConfig.js');
                                $view->getJsManager()->loadjsFile('app/modules/addresses/address.js');
                                $view->getJsManager()->loadjsFile('app/modules/promotion/promotion.js');
//            $view->getJsManager()->loadJsFile('app/modules/actividadesChofer/ActividadesChoferConfig.js');
//            $view->getJsManager()->loadJsFile('app/modules/actividadesChofer/actividadesChofer.js');
                                
                                $view->getJsManager()->loadJsFile('app/modules/inventoryWarehouseman/InventoryWarehousemanConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/inventoryWarehouseman/inventoryWarehouseman.js');
                                $view->getJsManager()->loadJsFile('app/modules/zone/zone.js');
                                $view->getJsManager()->loadJsFile('app/modules/cardOperators/cardOperators.js');
                                $view->getJsManager()->loadJsFile('app/modules/estados/EstadosConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/estados/estados.js');
                                $view->getJsManager()->loadJsFile('app/modules/ciudades/CiudadesConfig.js');
                                $view->getJsManager()->loadJsFile('app/modules/ciudades/ciudades.js');
                                $view->getJsManager()->loadjsFile('app/modules/promotionSchedule/promotionSchedule.js');
                                $view->getJsManager()->loadjsFile('app/modules/promotionReceived/promotionReceived.js');
                               
                                $view->getJsManager()->loadJsFile('app/config.js');
                                $view->getJsManager()->loadJsFile('app/app.js');
                                
                                $view->getJsManager()->loadJsFile('dist/webqr.js');//////////////para nuevo lector de qr
                                $view->getJsManager()->loadJsFile('https://apis.google.com/js/plusone.js');//////////////para nuevo lector de qr
                                $view->getJsManager()->loadJsFile('dist/llqrcode.js');//////////////para nuevo lector de qr
                                //$view->getJsManager()->loadJsFile('dist/photocam.js');
                                
                                
                                
                                ?>
                                </body>
                                </html>