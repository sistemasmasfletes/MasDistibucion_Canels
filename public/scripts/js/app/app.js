(function(){

    'use strict';

    var mdApp = angular.module("masDistribucion", [

        'masDistribucion.dep',

        'masDistribucion.routes',

        'masDistribucion.vehicles',

        'masDistribucion.points',

        'masDistribucion.users',

        'masDistribucion.schedule',

        'masDistribucion.scheduledRoute',

        'masDistribucion.routeSummary',

        'masDistribucion.warehouseman',

        'masDistribucion.causes',

        'masDistribucion.activityType',

        'masDistribucion.transactionType',

        'masDistribucion.report',

        'masDistribucion.inventory',

        'masDistribucion.customSelect',

        "ui.bootstrap",

        "ui.router",

        "ngCookies",

        "http-auth-interceptor",

        'masDistribucion.bancos',

        'masDistribucion.tipoPagos',

        'masDistribucion.tipoMovimientos',

        'masDistribucion.estatus',

        'masDistribucion.tipoMonedas',

        'masDistribucion.paises',

        'masDistribucion.cuentas',

        'masDistribucion.conversion',

        'masDistribucion.compraCreditos',

        'masDistribucion.aprobacionCreditos',

        'masDistribucion.transferenciaCreditos',

        'masDistribucion.balanceGeneral',

        'masDistribucion.pagos',

        'masDistribucion.actividaChoferLog',

        'masDistribucion.aprobacionCreditoControlador',

        'masDistribucion.administracionLogCliente',

        'masDistribucion.clientPackage',

        'masDistribucion.packageRate',

        'masDistribucion.configuration',

        'masDistribucion.addresses',

        'masDistribucion.promotion',

        'masDistribucion.inventoryWarehouseman',

        'masDistribucion.zone',

        'masDistribucion.cardOperators',

        'masDistribucion.estados',

        'masDistribucion.ciudades',

        'masDistribucion.contracts',
        'masDistribucion.promotionSchedule',
        'masDistribucion.promotionReceived'

    ]);

    

    mdApp        

        .config(['$httpProvider','$stateProvider', '$urlRouterProvider','$locationProvider','CONFIG',config])



        .run(['$http','$rootScope','$state','$stateParams','CONFIG',function($http,$rootScope,$state,$stateParams,CONFIG){

            $rootScope.logout=CONFIG.PATH+'/Index/logout';



            $rootScope.state=$state;

            $rootScope.stateParams=$stateParams;

            

        }])



        .controller('InitController',['$scope','$state','$cookieStore','$timeout','CONFIG','SessionData','RoutesDataService',InitController]);

        

    

})();

