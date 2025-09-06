<?php

class View_Helper_MenuHelper extends Model3_View_Helper {

    private $_cr;
    
    public function display() 
    {
        if (Model3_Auth::isAuth()) 
        {   
            
            $this->_cr = Model3_Auth::getCredentials();
            $currentUrl =  $this->_view->getRequest()->getRequestUri();            
            //Determinar qué diseño de menu mostrar. 
            //La aplicación basada en vistas Model3 utiliza Bootstrap 2, la que está basada en Angularjs, Bootstrap 3. 
            if(!(strpos($currentUrl,"/App/")>-1)){
				$this->Menuresponsive($this->_cr["id"],$this->_cr["role"]);          
            	$this->displayOldMenu($this->_cr["id"],$this->_cr["role"]);
            }else{
                $this->displayMainMenu($this->_cr["id"],$this->_cr["role"]);
            }
            
            echo '<div class="loader"></div>';
        } 
        else 
        {
            $this->displayMenuNone();
        }
    }

    public function displayResponsive()
    {
        if (Model3_Auth::isAuth())
        {
            $this->_cr = Model3_Auth::getCredentials();
            $dbs = Model3_Registry::getInstance()->get('databases');
            $em = $dbs['DefaultDb'];
            $user = $em->find('DefaultDb_Entities_User', $this->_cr['id']);
            switch ($this->_cr['type'])
            {
                case DefaultDb_Entities_User::USER_ADMIN :
                    $this->displayMenuAdminResponsive();
                    break;
                case DefaultDb_Entities_User::USER_DRIVER:
                    $this->displayMainMenu();
                    break;
                case DefaultDb_Entities_User::USER_CLIENT:
                    $this->displayMenuClient();
                    break;
                case DefaultDb_Entities_User::USER_STORER:
                    $this->displayMenuStorerResponsive();
                    break;
                case DefaultDb_Entities_User::USER_OPERATION_CONTROLLER:
                    //$this->displayMenuOperationControllerResponsive();
                    $this->displayMainMenu();
                    break;
                default:
                    Model3_Auth::deleteCredentials();
                    $this->displayMenuNone();
                    break;
            }
        }
        else
        {
            $this->displayMenuNone();
        }
    }

    private function printLogOut($cr)
    {
        ?>
        <ul class="nav navbar-nav navbar-right">
            <li><a>Usuario: <?php echo $cr['username']; ?></a></li>
            <li><a href="#" title="cerrar sesion" id="sessionExit">Salir <i class="icon-off icon-white" ></i> </a></li>
        </ul>
        <?php
    }
    
    private function printLogOut2($cr)
    {
        ?>
        <ul class="nav navbar-nav navbar-right">
            <li><a>Usuario: <?php echo $cr['username']; ?></a></li>
            <li><a href="#" title="cerrar sesion" id="sessionExit">Salir <i class="pers-btn icono-cerrar-sesion tam-normal" ></i> </a></li>
        </ul>
        <?php
    }
    

    private function printLogOutResponsive($cr)
    {
        ?>
        <ul class="nav navbar-nav pull-right">
            <li><a>Usuario: <?php echo $cr['username']; ?></a></li>
            <li><a href="#" title="cerrar sesion" id="sessionExit"><i class="icon-off icon-white" ></i> Salir</a></li>
        </ul>
        <?php
    }

    public function getMenu($userId,$roleId){
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];

        $menuRepo = $em->getRepository('DefaultDb_Entities_Menu');
        $menu = $menuRepo->getMenu($userId,$roleId);
        unset($em);
        return $menu;
       
    }

    public function displayMainMenu($userId,$roleId){
        //Este menú se inserta en las pantallas que funcionan con Bootstrap 3 (Módulos nuevos)
        ?>
        <nav class="navbar navbar-default menuWrapper" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                  <span class="sr-only">Desplegar navegación</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img  style="max-width: 2em;" src="<?php  echo $this->_view->getBaseUrl('/images/iconos/logomd-blanco.png') ?>" /></a>
            </div>
            <div class="collapse navbar-collapse">

        <?php

        //Obtener menu  ==========================================
        $menu = $this->getMenu($userId,$roleId);

        //Menu HTML  ==========================================
        $html = array();
        $root_id = null;
        
        foreach ($menu as $item ){        
            $children[$item['parent_id']][] = $item;
        }
       
        // Loop será falso si el nodo principal no tiene submenus. (menu vacío)
        $loop = !empty( $children[$root_id] );

        // Inicializar  $parent como la raíz
        $parent = $root_id;
        $parent_stack = array();

        $applicationPath = $this->_view->url(array('module'=>'','controller'=>'App')).'#!/';
        $baseUrl = $this->_view->getBaseUrlPublic();
        // HTML tag de abrir para el menu
        $html[] = '<ul class="nav navbar-nav">';

        while ( $loop && ( ( $option = each( $children[$parent] ) ) || ( $parent > $root_id ) ) )
        {
            if ( $option === false )
            {
                $parent = array_pop( $parent_stack );

                // HTML para menu que contiene submenu (tags de cerrar)
                $html[] = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 ) . '</ul>';
                $html[] = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ) . '</li>';
            }
            elseif ( !empty( $children[$option['value']['id']] ) )
            {
                $tab = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 );

                // HTML para menu que contiene submenu (tags de abrir)
                $html[] = sprintf(
                    '%1$s<li><a  data-toggle="tab" href="%2$s">%3$s <b class="caret"></b> </a>',
                    $tab,   // %1$s = tabulación
                    $option['value']['url'],    // %2$s = url
                    $option['value']['title']   // %3$s = titulo
                ); 
                $html[] = $tab . "\t" . '<ul class="dropdown-menu">';

                array_push( $parent_stack, $option['value']['parent_id'] );
                $parent = $option['value']['id'];
            }
            else {// HTML para menu sin hijos
                $url = $option['value']['url'];
                $appPath = $baseUrl."/";
                if(!((strpos($url, 'Admin') > -1)||(strpos($url, 'User') > -1)))                    
                    $appPath = $applicationPath;
                

                $html[] = sprintf(
                    '%1$s<li><a href="'.$appPath.'%2$s">%3$s</a></li>',
                    str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ),   //tabulación
                    $url,    // %2$s = url
                    $option['value']['title']   // %3$s = título
                );
            }
        }

        // HTML tag de cierre de menu
        $html[] = '</ul>';
        echo implode( "\r\n", $html );

        //Menu HTML  ==========================================

        $this->printLogOut2($this->_cr); 

        ?>
            </div>
        </nav>
        <?php
    }

    public function displayOldMenu($userId,$roleId){
        //Este menú se inserta en las pantallas que funcionan con Bootstrap 2 (Módulos con vistas PHP),
        //sin embargo, los datos son los mismos que para el menu de los módulo nuevos.
        ?>
        <div id="menuWrapper" >
            <div class="container ancho-maximo">
        		<div id="menu">
        
        <div class="navbar">
            <div class="navbar-inner ancho-max">
                <div class="container mxx">
                    <div class="nav-collapse">
                        <a href="#" class="navbar-brand pull-left"><img style="margin:0px; padding:0px; height:2.5em;" src="/public/images/iconos/logomd-blanco.png" /></a>
        <?php

        //Obtener menu  ==========================================
        $menu = $this->getMenu($userId,$roleId);


        //Menu HTML  ==========================================
        $html = array();
        $root_id = null;
        
        foreach ($menu as $item ){        
            $children[$item['parent_id']][] = $item;
        }
       
        // Loop será falso si el nodo principal no tiene submenus. (menu vacío)
        $loop = !empty( $children[$root_id] );

        // Inicializar  $parent como la raíz
        $parent = $root_id;
        $parent_stack = array();

        $applicationPath = $this->_view->url(array('module'=>'','controller'=>'App')).'#!/';
        $baseUrl = $this->_view->getBaseUrlPublic();
        // HTML tag de abrir para el menu
        $html[] = '<ul class="nav navbar-nav sm">';

        while ( $loop && ( ( $option = each( $children[$parent] ) ) || ( $parent > $root_id ) ) )
        {
            if ( $option === false )
            {
                $parent = array_pop( $parent_stack );

                // HTML para menu que contiene submenu (tags de cerrar)
                $html[] = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 ) . '</ul>';
                $html[] = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ) . '</li>';
            }
            elseif ( !empty( $children[$option['value']['id']] ) )
            {
                $tab = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 );

                // HTML para menu que contiene submenu (tags de abrir)
                $html[] = sprintf(
                    '%1$s<li class="dropdown navbar"><a href="%2$s" class="dropdown-toggle" data-toggle="dropdown">%3$s <b class="caret"></b> </a>',
                    $tab,   // %1$s = tabulación
                    $option['value']['url'],    // %2$s = url
                    $option['value']['title']   // %3$s = titulo
                ); 
                $html[] = $tab . "\t" . '<ul class="dropdown-menu">';

                array_push( $parent_stack, $option['value']['parent_id'] );
                $parent = $option['value']['id'];
            }
            else {// HTML para menu sin hijos
                $url = $option['value']['url'];
                $appPath = $baseUrl."/";
                if(!((strpos($url, 'Admin') > -1)||(strpos($url, 'User') > -1)))                    
                    $appPath = $applicationPath;
                
                $html[] = sprintf(
                    '%1$s<li class="navbar-no-menu"><a href="'.$appPath.'%2$s">%3$s</a></li>',
                    str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ),   //tabulación
                    $url,    // %2$s = url
                    $option['value']['title']   // %3$s = título
                );
            }
        }

        // HTML tag de cierre de menu
        $html[] = '</ul>';
        echo implode( "\r\n", $html );

        //Menu HTML  ==========================================

        $this->printLogOut2($this->_cr); 

        ?>
                    	</div>
                	</div>
            	</div>
        	</div>
    	</div><!-- end of #menu -->
    	</div>
 	</div>
    	
    	            
   	<?php
    }

    public function displayMenuOperationControllerResponsive()
    {
        ?>
        <nav class="navbar navbar-default" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                  <span class="sr-only">Desplegar navegación</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Mas distribuci&oacute;n</a>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li  class="active"><a data-toggle="tab"  href="<?php
                        echo $this->_view->url(array(
                            'module' => 'OperationController',
                            'controller' => 'Dashboard'
                            ))
                        ?>" ><?php echo ($this->_view->TrHelper()->_('Inicio')); ?></a> </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Tiendas'); ?> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <!--<li><a href="<?php // echo $this->_view->url(array('module' => 'OperationController', 'controller' => 'PackagesTypes ))                                  ?>" ><?php // echo $this->_view->TrHelper()->_('Paquetes');                                  ?></a></li>-->
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'OperationController',
                                    'controller' => 'Categories'
                                    ))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Categorias'); ?></a></li>
                            <li class="divider"></li>
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'OperationController',
                                    'controller' => 'Reports',
                                    'action' => 'orders'))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Ordenes'); ?></a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Rutas'); ?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php
                                echo $this->_view->url(array('module'=>'','controller'=>'App')).'#!/routes';//$this->_view->url(array(
                                    //'module' => 'OperationController',
                                    //'controller' => 'Routes'
                                    //))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Rutas'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'OperationController',
                                    'controller' => 'Calendar'
                                    ))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Calendario'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array('module'=>'','controller'=>'App')).'#!/schedule';//$this->_view->url(array(
                                    //'module' => 'OperationController',
                                    //'controller' => 'Schedule'
                                    //))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Programación'); ?></a></li>
                            <li class="divider"></li>
                            <li><a href="<?php
                                echo $this->_view->url(array('module'=>'','controller'=>'App')).'#!/scheduledRoutes';//$this->_view->url(array(
                                    //'module' => 'OperationController',
                                    //'controller' => 'Reports',
                                    //'action' => 'secuencialActivity'))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Reporte de Actividades'); ?></a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Sistema'); ?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php
                                echo $this->_view->url(array('module'=>'','controller'=>'App')).'#!/vehicles';
                                    /*$this->_view->url(array(
                                    'module' => 'OperationController',
                                    'controller' => 'Vehicles'
                                    ))*/
                                ?>" ><?php echo $this->_view->TrHelper()->_('Vehículos'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array('module'=>'','controller'=>'App')).'#!/points';//$this->_view->url(array(
                                    //'module' => 'OperationController',
                                    //'controller' => 'Points'
                                    //))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Puntos de Venta'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array('module'=>'','controller'=>'App')).'#!/users';//$this->_view->url(array(
                                    //'module' => 'OperationController',
                                    //'controller' => 'Users'
                                    //))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Usuarios'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'OperationController',
                                    'controller' => 'Users'))."#!/Storer"
                                ?>" ><?php echo $this->_view->TrHelper()->_('Almacenistas'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'OperationController',
                                    'controller' => 'Users'))."#!/Driver"
                                ?>" ><?php echo $this->_view->TrHelper()->_('Conductores'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'OperationController',
                                    'controller' => 'InvoicesUsers'
                                    ))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Facturacion'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'OperationController',
                                    'controller' => 'Email'
                                    ))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Correos'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'OperationController',
                                    'controller' => 'ConfigSystem'
                                    ))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Configuracion'); ?></a></li>
                        </ul>
                    </li>
                </ul>
                <?php $this->printLogOut2($this->_cr); ?>
            </div>
        </nav>
        <?php
    }
    
    private function displayMenuAdmin()
    {
        ?>
        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">
                    <div class="nav-collapse">
                        <ul class="nav">
                            <li><a data-toggle="tab"   href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'Admin',
                                    'controller' => 'Dashboard',
                                    'action' => 'index'))
                                ?>" ><?php echo ($this->_view->TrHelper()->_('Inicio')); ?></a> </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Tiendas'); ?> <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <!--<li><a href="<?php // echo $this->_view->url(array('module' => 'Admin', 'controller' => 'PackagesTypes', 'action' => 'index'))                                  ?>" ><?php // echo $this->_view->TrHelper()->_('Paquetes');                                  ?></a></li>-->
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Categories',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Categorias'); ?></a></li>
                                    <li class="divider"></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Reports',
                                            'action' => 'orders'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Ordenes'); ?></a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Rutas'); ?><b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Routes',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Rutas'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Calendar',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Calendario'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Program',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Programación'); ?></a></li>
                                    <li class="divider"></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Reports',
                                            'action' => 'secuencialActivity'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Reporte de Actividades'); ?></a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Sistema'); ?><b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Vehicles',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Vehículos'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'AdminUsers',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Usuarios'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Storer',
                                            'controller' => 'Admin',
                                            'action' => 'users'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Almacenistas'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'InvoicesUsers',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Facturacion'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Email',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Correos'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'ConfigSystem',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Configuracion'); ?></a></li>
                                </ul>
                            </li>
                        </ul>
                        <?php $this->printLogOut($this->_cr); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function displayMenuStorerResponsive()
    {
        ?>
        <div class="navbar navbar-default navbar-fixed-top" style="margin-bottom: 0px;">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a data-toggle="tab"   href="<?php
                            echo $this->_view->url(array(
                                'module' => 'Storer',
                                'controller' => 'Dashboard',
                                'action' => 'index'))
                            ?>" >
                                   <?php echo ($this->_view->TrHelper()->_('Inicio')); ?>
                            </a> 
                        </li>
                    </ul>
                    <?php $this->printLogOutResponsive($this->_cr); ?>
                </div>
            </div>
        </div>
        <?php
    }

    private function displayMenuOperationControllerResponsive2()
    {
        ?>
        <div class="navbar navbar-default navbar-fixed-top" style="margin-bottom: 0px;">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a data-toggle="tab"   href="<?php
                            echo $this->_view->url(array(
                                'controller' => 'Dashboard',
                                'action' => 'index',))
                            ?>">
                                   <?php echo ($this->_view->TrHelper()->_('Inicio')); ?>
                            </a> 
                        </li>
                        <?php $session = new Model3_Session_Namespace('operationController'); ?>
                        <?php if ($session->required === true) : ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="<?php
                                echo $this->_view->url(array(
                                    'controller' => 'Dashboard',
                                    'action' => 'resources',))
                                ?>" >
                                       <?php echo ($this->_view->TrHelper()->_('Recursos')); ?>
                                    <b class="caret"></b>
                                </a> 
                                <ul class="dropdown-menu">
                                    <?php
                                    $driversPath = $this->_view->url(array('module' => 'OperationController',
                                        'controller' => 'Dashboard', 'action' => 'drivers'));
                                    $vehiclesPath = $this->_view->url(array('module' => 'OperationController',
                                        'controller' => 'Vehicles'));
                                    $routesPath = $this->_view->url(array('module' => 'OperationController',
                                        'controller' => 'Dashboard', 'action' => 'routes'));
                                    ?>
                                    <li><a href="<?php echo $driversPath; ?>">Conductores</a></li>
                                    <li><a href="<?php echo $vehiclesPath; ?>">Vehículos</a></li>
                                    <li><a href="<?php echo $routesPath; ?>">Rutas</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Rutas'); ?><b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Routes',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Rutas'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Calendar',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Calendario'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Program',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Programación'); ?></a></li>
                                    <li class="divider"></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'Admin',
                                            'controller' => 'Reports',
                                            'action' => 'secuencialActivity'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Reporte de Actividades'); ?></a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'OperationController',
                                    'controller' => 'Dashboard',
                                    'action' => 'evalUser',))
                                ?>">
                                       <?php echo ($this->_view->TrHelper()->_('Evaluación de personal')); ?>
                                </a> 
                            </li>
                        <?php endif; ?>
                    </ul>
                    <?php $this->printLogOutResponsive($this->_cr); ?>
                </div>
            </div>
        </div>
        <?php
    }

    private function displayMenuAdminResponsive()
    {
        ?>
        <div class="navbar navbar-default navbar-fixed-top" style="margin-bottom: 0px;">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!--                    <a class="navbar-brand" href="#">
                                            <img src="<?php echo $this->_view->getBaseUrl('/images/logo-masfletes.gif'); ?>" />
                                        </a>-->
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a data-toggle="tab"   href="<?php
                            echo $this->_view->url(array(
                                'module' => 'Admin',
                                'controller' => 'Dashboard',
                                'action' => 'index'))
                            ?>" >
                                   <?php echo ($this->_view->TrHelper()->_('Inicio')); ?>
                            </a> 
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Tiendas'); ?> <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <!--<li><a href="<?php // echo $this->_view->url(array('module' => 'Admin', 'controller' => 'PackagesTypes', 'action' => 'index'))                                 ?>" ><?php // echo $this->_view->TrHelper()->_('Paquetes');                                 ?></a></li>-->
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'Categories',
                                        'action' => 'index'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Categorias'); ?></a></li>
                                <li class="divider"></li>
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'Reports',
                                        'action' => 'orders'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Ordenes'); ?></a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Rutas'); ?><b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'Routes',
                                        'action' => 'index'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Rutas'); ?></a></li>
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'ExchangeCenters',
                                        'action' => 'index'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Centros de intercambio'); ?></a></li>
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'Calendar',
                                        'action' => 'index'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Calendario'); ?></a></li>
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'Program',
                                        'action' => 'index'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Programación'); ?></a></li>
                                <li class="divider"></li>
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'Reports',
                                        'action' => 'secuencialActivity'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Reporte de Actividades'); ?></a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Sistema'); ?><b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'Vehicles',
                                        'action' => 'index'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Vehículos'); ?></a></li>
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'AdminUsers',
                                        'action' => 'index'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Usuarios'); ?></a></li>
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Storer',
                                        'controller' => 'Admin',
                                        'action' => 'users'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Almacenistas'); ?></a></li>
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'InvoicesUsers',
                                        'action' => 'index'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Facturacion'); ?></a></li>
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'Email',
                                        'action' => 'index'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Correos'); ?></a></li>
                                <li><a href="<?php
                                    echo $this->_view->url(array(
                                        'module' => 'Admin',
                                        'controller' => 'ConfigSystem',
                                        'action' => 'index'))
                                    ?>" ><?php echo $this->_view->TrHelper()->_('Configuracion'); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                    <?php $this->printLogOutResponsive($this->_cr); ?>
                    <!--                    <ul class="nav navbar-nav navbar-right">
                                            <li>
                                                <a title="Panel para ingreso de usuarios" href="<?php
                    echo
                    $this->_view->url(array(
                        'controller' => 'Sesion', 'action' => 'Index'));
                    ?>">
                                                    Iniciar sesión
                                                </a>
                                            </li>
                                        </ul>-->
                </div>
            </div>
        </div>

        <!--        <div class="navbar">
                    <div class="navbar-inner">
                        <div class="container">
                            <div class="nav-collapse">
                                <ul class="nav">
                                    <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'Dashboard',
            'action' => 'index'))
        ?>" ><?php echo ($this->_view->TrHelper()->_('Inicio')); ?></a> </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Tiendas'); ?> <b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php // echo $this->_view->url(array('module' => 'Admin', 'controller' => 'PackagesTypes', 'action' => 'index'))                                 ?>" ><?php // echo $this->_view->TrHelper()->_('Paquetes');                                 ?></a></li>
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'Categories',
            'action' => 'index'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Categorias'); ?></a></li>
                                            <li class="divider"></li>
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'Reports',
            'action' => 'orders'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Ordenes'); ?></a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Rutas'); ?><b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'Routes',
            'action' => 'index'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Rutas'); ?></a></li>
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'Calendar',
            'action' => 'index'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Calendario'); ?></a></li>
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'Program',
            'action' => 'index'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Programación'); ?></a></li>
                                            <li class="divider"></li>
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'Reports',
            'action' => 'secuencialActivity'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Reporte de Actividades'); ?></a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Sistema'); ?><b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'Vehicles',
            'action' => 'index'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Vehículos'); ?></a></li>
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'AdminUsers',
            'action' => 'index'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Usuarios'); ?></a></li>
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'InvoicesUsers',
            'action' => 'index'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Facturacion'); ?></a></li>
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'Email',
            'action' => 'index'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Correos'); ?></a></li>
                                            <li><a href="<?php
        echo $this->_view->url(array(
            'module' => 'Admin',
            'controller' => 'ConfigSystem',
            'action' => 'index'))
        ?>" ><?php echo $this->_view->TrHelper()->_('Configuracion'); ?></a></li>
                                        </ul>
                                    </li>
                                </ul>
        <?php $this->printLogOut($this->_cr); ?>
                            </div>
                        </div>
                    </div>
                </div>-->
        <?php
    }

    private function displayMenuClient()
    {
        ?>
        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">
                    <div class="nav-collapse">
                        <ul class="nav">
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'User',
                                    'controller' => 'Dashboard',
                                    'action' => 'index'))
                                ?>" ><i class="icon-home icon-white"></i><?php echo ($this->_view->TrHelper()->_('Inicio')); ?></a> </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-th-list icon-white"></i><?php echo $this->_view->TrHelper()->_('Mi panel'); ?> <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'User',
                                            'controller' => 'Catalogos',
                                            'action' => 'index'))
                                        ?>" ><i class="icon-book"></i><?php echo $this->_view->TrHelper()->_('Catalogos'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'User',
                                            'controller' => 'InvoicesUsers',
                                            'action' => 'index'))
                                        ?>" ><i class="icon-book"></i><?php echo $this->_view->TrHelper()->_('Facturacion'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'User',
                                            'controller' => 'BackStore',
                                            'action' => 'orders'))
                                        ?>" ><i class="icon-folder-close"></i><?php echo $this->_view->TrHelper()->_('Ventas'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'User',
                                            'controller' => 'BackStore',
                                            'action' => 'shopping'))
                                        ?>" ><i class="icon-lock"></i><?php echo $this->_view->TrHelper()->_('Compras'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'User',
                                            'controller' => 'BranchesUser',
                                            'action' => 'index'))
                                        ?>" ><i class="icon-lock"></i><?php echo $this->_view->TrHelper()->_('Sucursales'); ?></a></li>
                                    <li class="divider"></li>

                                </ul>
                            </li>
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'User',
                                    'controller' => 'Store',
                                    'action' => 'index'))
                                ?>" ><i class="icon-tags icon-white"></i><?php echo $this->_view->TrHelper()->_('Tiendas'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'User',
                                    'controller' => 'Store',
                                    'action' => 'viewCart'))
                                ?>" ><i class="icon-shopping-cart icon-white"></i><?php echo $this->_view->TrHelper()->_('Carrito'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array(
                                    'module' => 'User',
                                    'controller' => 'FavoriteUsers',
                                    'action' => 'index'))
                                ?>" ><i class="icon-star icon-white"></i><?php echo $this->_view->TrHelper()->_('Favoritos'); ?></a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_($this->_cr['username']); ?><b class="caret"></b></a>
                                <ul class="dropdown-menu" nav pull-right>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'User',
                                            'controller' => 'UserProfile',
                                            'action' => 'index'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Datos Personales'); ?></a></li>
                                    <li><a href="<?php
                                        echo $this->_view->url(array(
                                            'module' => 'User',
                                            'controller' => 'UserProfile',
                                            'action' => 'password'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Passsword'); ?></a></li>
                                    <li><a href="#" id="sessionExit"><?php echo $this->_view->TrHelper()->_('Salir'); ?></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function displayMenuDriver()
    {
        ?>
     <nav class="navbar navbar-default" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                  <span class="sr-only">Desplegar navegación</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Mas distribuci&oacute;n</a>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li  class="active"><a  data-toggle="tab"  href="<?php
                        echo $this->_view->url(array(
                            'module' => 'OperationController',
                            'controller' => 'Dashboard'
                            ))
                        ?>" ><?php echo ($this->_view->TrHelper()->_('Inicio')); ?></a> </li>

                    <!-- menú nuevo -->                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_view->TrHelper()->_('Sistema'); ?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php
                                echo $this->_view->url(array('module'=>'','controller'=>'App')).'#!/routeSummary';//$this->_view->url(array(
                                        //'module' => 'Driver',
                                        //'controller' => 'RouteSummaryController',
                                        //'action' => 'secuencialActivity'))
                                        ?>" ><?php echo $this->_view->TrHelper()->_('Rutas'); ?>
                                </a>
                            </li>
                            <li><a href="<?php
                                echo $this->_view->url(array('module'=>'','controller'=>'App')).'#!/points';//$this->_view->url(array(
                                    //'module' => 'OperationController',
                                    //'controller' => 'Points'
                                    //))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Inventario de vehículo'); ?></a></li>
                            <li><a href="<?php
                                echo $this->_view->url(array('module'=>'','controller'=>'App')).'#!/users';//$this->_view->url(array(
                                    //'module' => 'OperationController',
                                    //'controller' => 'Users'
                                    //))
                                ?>" ><?php echo $this->_view->TrHelper()->_('Reporte de actividades'); ?></a></li>
                        </ul>
                    </li>
                    
                    <!-- -->
                    
                </ul>
                <?php $this->printLogOut2($this->_cr); ?>
            </div>
        </nav>
        <?php
    }

    // Oculta menu
    private function displayMenuNone()
    {
        ?>
        <script type="text/javascript">
            $("#menuWrapper").hide();
        </script>
        <?php
    
		echo '
				<div class="menu-btn-commercial">
					<img class="nav-brand-commercial" src="'.$this->_view->getBaseUrl('/images/iconos/list-opcion-wh.png').'" />
				</div>
				<nav class="nav-main-commercial">
					<a class="nav-brand-commercial" href="'.$this->_view->getBaseUrl('/Index/index').'"><img src="'.$this->_view->getBaseUrl('/images/iconos/homewhite.png').'" /></a>
					<ul class="nav-menu-commercial">
						<li><a href="'.$this->_view->getBaseUrl('/Index/index').'">Inicio</a></li>
						<li><a href="'.$this->_view->getBaseUrl('/Index/register').'">Registro</a></li>
						<!--li><a href="'.$this->_view->getBaseUrl('/Index/pantry').'">Despensa</a></li-->
						<li><a href="'.$this->_view->getBaseUrl('/Index/searchProduct').'"><img style="width:15px;height:15px;margin-right:0.5em;" src="'.$this->_view->getBaseUrl('/images/iconos/icon-search.png').'"/>Buscar</a></li>
            			<li class="socialico"><a href="https://www.facebook.com/masdistribucion" target="_blank"><img src="'.$this->_view->getBaseUrl('/images/iconos/facebookico.png').'"/></a></li>
            			<li class="socialico"><a href="https://www.youtube.com/channel/UCia4jn3N7nXhxiX9CW4WaIw" target="_blank"><img src="'.$this->_view->getBaseUrl('/images/iconos/youtubeico.png').'"/></a></li>
						<li class="socialico"><a href="https://instagram.com/mas.distribucion?igshid=1e5qkqugeidz8" target="_blank"><img src="'.$this->_view->getBaseUrl('/images/iconos/instagram.png').'"/></a></li>
            		</ul>
					<ul class="nav-menu-right-commercial">
            			<li><a href="https://www.facebook.com/masdistribucion" target="_blank"><img src="'.$this->_view->getBaseUrl('/images/iconos/facebookico.png').'"/></a></li>
            			<li><a href="https://www.youtube.com/channel/UCia4jn3N7nXhxiX9CW4WaIw" target="_blank"><img src="'.$this->_view->getBaseUrl('/images/iconos/youtubeico.png').'"/></a></li>
						<li><a href="https://instagram.com/mas.distribucion?igshid=1e5qkqugeidz8" target="_blank"><img src="'.$this->_view->getBaseUrl('/images/iconos/instagram.png').'"/></a></li>
					</ul>
				</nav>
            	<div id="page" style="">';
	}
    
    // Muestra Inicio en pantalla login
    private function displayMenuNone_OLD()
    {
        ?>
        <div class="navbar ">
            <div class="navbar-inner">
                <div class="container">
                    <ul class="nav">
                        <li><a data-toggle="tab"   href="<?php
                            echo $this->_view->url(array(
                                'module' => false,
                                'controller' => 'Index',
                                'action' => 'index'))
                            ?>" ><?php echo ($this->_view->TrHelper()->_('Inicio')); ?></a> </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function Menuresponsive($userId,$roleId){

		echo '
			<!-- --------------------------------------------MENU PARA DISPOSITIVOS-------------------------------------------------------------------------------- -->
			<a id="touch-menu" class="mobile-menu" href="#">
			<img style="margin:0px; padding:0px; height:4em;" src="'.$this->_view->getBaseUrl('/images/iconos/logomd-blanco.png').'" /><img style="float:right; height:4em;margin-top:-0.8em;
					" src="' . $this->_view->getBaseUrl('/images/iconos/list-opcion-wh.png') . '" />
			</a>
			<nav>
            		
            		';
            		
		//Obtener menu  ==========================================
		$menu = $this->getMenu($userId,$roleId);
		
		
		//Menu HTML  ==========================================
		$html = array();
		$root_id = null;
		
		foreach ($menu as $item ){
			$children[$item['parent_id']][] = $item;
		}
		 
		// Loop será falso si el nodo principal no tiene submenus. (menu vacío)
		$loop = !empty( $children[$root_id] );
		
		// Inicializar  $parent como la raíz
		$parent = $root_id;
		$parent_stack = array();
		
		$applicationPath = $this->_view->url(array('module'=>'','controller'=>'App')).'#!/';
		$baseUrl = $this->_view->getBaseUrlPublic();
		// HTML tag de abrir para el menu
		$html[] = '<ul class="menu">';
		
		while ( $loop && ( ( $option = each( $children[$parent] ) ) || ( $parent > $root_id ) ) )
		{
			if ( $option === false )
			{
				$parent = array_pop( $parent_stack );
		
				// HTML para menu que contiene submenu (tags de cerrar)
				$html[] = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 ) . '</ul>';
				$html[] = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ) . '</li>';
			}
			elseif ( !empty( $children[$option['value']['id']] ) )
			{
				$tab = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 );
		
				// HTML para menu que contiene submenu (tags de abrir)
				$html[] = sprintf(
						'%1$s<li><button><span style="float:left;">%3$s</span><img style="float:right;width:10%;height:1em" src="'.$this->_view->getBaseUrl('/images/iconos/pluswhite.png').'" /></button>',
						$tab,   // %1$s = tabulación
						$option['value']['url'],    // %2$s = url
						$option['value']['title']   // %3$s = titulo
				);
				$html[] = $tab . "\t" . '<ul class="sub-menu">';
		
				array_push( $parent_stack, $option['value']['parent_id'] );
				$parent = $option['value']['id'];
			}
			else {// HTML para menu sin hijos
				$url = $option['value']['url'];
				$appPath = $baseUrl."/";
				if(!((strpos($url, 'Admin') > -1)||(strpos($url, 'User') > -1)))
					$appPath = $applicationPath;
		
				$html[] = sprintf(
						'%1$s<li><a href="'.$appPath.'%2$s">%3$s</a></li>',
						str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ),   //tabulación
						$url,    // %2$s = url
						$option['value']['title']   // %3$s = título
				);
			}
		}
		
		//$html[] = '<li><a href="#" title="cerrar sesion" id="sessionExit">Salir</a></li>';
		
		// HTML tag de cierre de menu
		$html[] = '</ul>';
		echo implode( "\r\n", $html );
		
		echo '</nav>';
		//Menu HTML  ==========================================
		
		
		

		/*echo '
			<!-- --------------------------------------------MENU PARA DISPOSITIVOS-------------------------------------------------------------------------------- -->
			<a id="touch-menu" class="mobile-menu" href="#">
			<span>Mas distribuci&oacute;n</span><img style="float:right;width:10%;margin-top:-0.8em;
					" src="' . $this->_view->getBaseUrl('/images/iconos/list-opcion-wh.png') . '" />
			</a>
			<nav>
			<ul class="menu">
				<li><a href="">Inicio</a><li>
				<li><a href="">Empresas</a><li>
				<li><button>1. Proveedores</button>
						<ul class="sub-menu">
							<li><a href="">1.1 Operaciones Proveedores</a></li>
							<li><a href="">1.2 Proveedores de Transporte</a></li>
							<li><a href="">1.3 Proveedores Diversos</a></li>
						</ul>
					</li>
					<li><button>2. Clientes</button>
						<ul class="sub-menu">
							<li><a href="">2.1 Clientes de Transporte</a></li>
							<li><a href="">2.5 Ordenes de mis Clientes</a></li>
						</ul>
					</li>
					<li><a  href="">3. Operaciones</a></li>
					<li><button>4. Cotizador</button>
						<ul class="sub-menu">
							<li><a href="">Generadas</a></li>
							<li><a href="">Recibidos</a></li>
						</ul>
					</li>
					<li><a  href="">5. Publicaci&oacute;n</a></li>
					<li><button>6. Cat&aacute;logos</button>
						<ul class="sub-menu">
							<li><a href="" class="lang" key="unidades">Unidades</a></li>
							<li><a href="" class="lang" key="conductores">Conductores</a></li>
							<li><a href="" class="lang" key="ajuestesrastreo">Empresas de Rastreo</a></li>
							<li><a href="" class="lang" key="reportes">Reportes</a></li>
						</ul>
					</li>
				</ul>
			</nav>
		    <!-- -------------------------------------------------------------------------------------------------------------------------------------------------- -->
			';*/
	}

}
