<?php

class User_StoreController extends Model3_Controller {

    public function init() {
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }

        $this->getAddress();
    }

    function getAddress() {

        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        $ACTIVO = 1;
        $countries = $em->getRepository('DefaultDb_Entities_Paises')->findBy(array('estado' => $ACTIVO));
        $this->view->countries = $countries;
        $states = null;
        $cities = null;
        if ($this->view->country) {
            $states = $em->getRepository('DefaultDb_Entities_State')->findBy(array('country' => (int) $this->view->country, 'estatus' => $ACTIVO));
        }
        $this->view->states = $states;

        if ($this->view->state) {
            $cities = $em->getRepository('DefaultDb_Entities_City')->findBy(array('state' => (int) $this->view->state,'estatus' => $ACTIVO));
        }
        $this->view->cities = $cities;
    }

    public function indexAction() {
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->view->getJsManager()->addJs('application/User/Categories/getAddress.js');
        $this->view->getJsManager()->addJsVar('urlGetStates', '\'' . $this->view->url(array('module' => '', 'controller' => 'OperationController_Estados', 'action' => 'getEstadosByCountryId')) . '\'');
        $this->view->getJsManager()->addJsVar('urlGetCities', '\'' . $this->view->url(array('module' => '', 'controller' => 'OperationController_Ciudades', 'action' => 'getCitiesByStateId')) . '\'');
        $this->view->getJsManager()->addJsVar('urlSearch', json_encode($this->view->url(array('module'=>'Ajax','controller'=>'BackStore','action'=>'searchClients'))));
        $this->view->getJsManager()->addJs('view/scripts/User/BackStore/createOrder' . VERSION_JS . '.js');
        $this->view->getCssManager()->addCss('view/scripts/User/BackStore/listSearch' . VERSION_CSS . '.css');


        $params = $this->getRequest()->getPost();
        /*$countryId = isset($params["country"]) && $params["country"] != "" ? "u.country = " . $params["country"] . " " : " u.country is null or u.country > 0";
        $stateId = isset($params["state"]) && $params["state"] != "" ? "u.state = " . $params["state"] . " " : "u.state is null or u.state > 0";
        $cityId = isset($params["city"]) && $params["city"] != "" ? "u.city = " . $params["city"] . " " : "u.city is null or u.city > 0";*/
        
        $countryId = isset($params["country"]) && $params["country"] != "" ? "p.country = " . $params["country"] . " " : "";
        $stateId = isset($params["state"]) && $params["state"] != "" ? "p.state = " . $params["state"] . " " : "";
        $cityId = isset($params["city"]) && $params["city"] != "" ? "p.city = " . $params["city"] . " " : "";
        
        $filtroTiendasSucursal = isset($params["chkTiendasSucursal"]);        
        $sessionUserId = Model3_Auth::getCredentials('id');

        $em = $this->getEntityManager('DefaultDb');
        $qb = $em->createQueryBuilder();
        
         $wherePoint = "b.point >0 ";
         
        if(isset($params["hiddenBranch"])){
            if($params["hiddenBranch"] != ''){
                //$wherePoint = " p.id = '".$params["hiddenBranch"]."' ";
            	$wherePoint = " b.id = '".$params["hiddenBranch"]."' ";
                
                $this->view->hiddenBranch = $params["hiddenBranch"];
                $this->view->usernameTxt = $params["usernameTxt"];
            }
        }
        
        //if ($filtroTiendasSucursal || isset($params["hiddenBranch"])) {
        if (isset($params["hiddenBranch"]) && $params["hiddenBranch"] != '') {
        	$qb->select('c')
                    ->from('DefaultDb_Entities_Category', 'c')
                    ->leftJoin('c.users', 'u')
                    ->leftJoin('u.branches', 'b')
                    ->where($wherePoint)
                    ->orderBy('c.name', 'ASC');
            
            /*$qb->select('p')
            ->from('DefaultDb_Entities_Point', 'p')
            ->leftJoin('p.categoryId', 'c')
            ->Where($wherePoint)
            ->orderBy('c.name', 'ASC');*/
            $this->view->filterone = TRUE;
        }else {
            /*$qb->select('c')
                    ->from('DefaultDb_Entities_Category', 'c')
                    ->leftJoin('c.users', 'u')
                    ->leftJoin('u.branches', 'b')
                    ->where($countryId)
                    ->andWhere($stateId)
                    ->andWhere($cityId)
                    ->orderBy('c.name', 'ASC');*/
            
        	if($cityId != ""){
        		$qb->select('rp')
        		->from('DefaultDb_Entities_RoutePoint', 'rp')
        		->leftJoin('rp.point', 'p')
        		->leftJoin('p.categoryId','c')
        		->where(' c.id is not null')
        		->andWhere($cityId)
        		->groupBy('c.id')
        		->orderBy('c.name', 'ASC');
            }else{
            	
            	$qb->select('u')
            	->from('DefaultDb_Entities_User', 'u')
            	->leftJoin('u.category','c')
            	->where(' c.id is not null')
            	->groupBy('c.id')
            	->orderBy('c.name', 'ASC');

                /*$qb->select('rp')
	            ->from('DefaultDb_Entities_RoutePoint', 'rp')
	            ->leftJoin('rp.point', 'p')
	            ->leftJoin('p.categoryId','c')
	            ->where(' c.id is not null')
	            ->groupBy('c.id')
	            ->orderBy('c.name', 'ASC');*/
        	}
        }
        
        $this->view->check = $filtroTiendasSucursal;
        

        $query = $em->createQuery($qb);

        $this->view->categories = $query->getResult();
        $this->view->country = isset($params["country"]) ? $params["country"] : "";
        $this->view->state = isset($params["state"]) ? $params["state"] : "";
        $this->view->city = isset($params["city"]) ? $params["city"] : "";
        $this->getAddress();
    }

    public function viewCatalogAction() {
        $em = $this->getEntityManager('DefaultDb');

        $catalogId = $this->getRequest()->getParam('id');
        $catalog = $em->find('DefaultDb_Entities_Catalog', $catalogId);
        $this->view->catalog = $catalog;
        $this->view->user = $catalog->getClient();
        $this->view->category = $this->view->user->getCategory();
    }

    public function viewCartAction() {
        $commerce = new Model3_Session_Namespace('commerce');
        $cart = $commerce->cart;
        $idTienda = $this->getRequest()->getParam('id');
        $idRoutePoint = $this->getRequest()->getParam('routePointId');
        $action = $this->getRequest()->getAction();

        $em = $this->getEntityManager('DefaultDb');

        $branchesRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
        $userRepos = $em->getRepository('DefaultDb_Entities_User');
        $buyer = "";
        if ($cart) {
            $credentials = Model3_Auth::getCredentials();
            if ($credentials['type'] == DefaultDb_Entities_User::USER_DRIVER) {
                $viewUser = new Model3_Session_Namespace('viewUser');
                if($viewUser->id){
                    $buyer = $userRepos->find($viewUser->id);
                }
                if (isset($idRoutePoint)) {                    
                    $q = $em->getRepository('DefaultDb_Entities_RoutePoint')->createQueryBuilder('rp')->select('IDENTITY(rp.point)')->Where("rp.id='" . $idRoutePoint . "'")->getQuery()->getResult();
                    $bu = $branchesRepos->findOneBy(array('point' => $q[0][1] ),array('id' => 'desc'));
                    
                    if($bu){
                        $buyer = $bu->getClient();
                    }
                }
                 
            } else {
                $buyer = $userRepos->find($credentials['id']);
            }
            $tienda = $em->find('DefaultDb_Entities_User', $cart->getIdSeller());
            $this->view->tiendaSelected = $tienda;
            if($buyer){
                $branchesBuyer = $branchesRepos->findBy(array('client' => $buyer));
                $branchesSeller = $branchesRepos->findBy(array('client' => $cart->getIdSeller()));
                $this->view->branchesSeller = $branchesSeller;
                $this->view->branchesBuyer = $branchesBuyer;
            }
        }
        if ($idTienda) {
            $branchesSeller = $branchesRepos->findBy(array('client' => Model3_Auth::getCredentials('id')));
            $branchesBuyer = $branchesRepos->findBy(array('client' => $idTienda));
            $this->view->branchesSeller = $branchesSeller;
            $this->view->branchesBuyer = $branchesBuyer;
            $tienda = $em->find('DefaultDb_Entities_User', $idTienda);
            $this->view->tiendaSelected = $tienda;
        }
        
        $this->view->routePointId = $idRoutePoint;
        $this->view->action = $action;
    }

    public function confirmOrderAction() {
        $commerce = new Model3_Session_Namespace('commerce');
        $paymentResult = false;
        $cart = $commerce->cart;
        $post = $this->getRequest()->getPost();
        $idRoutePoint = $post["routePointId"];
        
        if ($cart || isset($post['store'])) {
            
            
            $em = $this->getEntityManager('DefaultDb');
            $em->getConnection()->beginTransaction();
            try {
                $favoriteUserRepos = $em->getRepository('DefaultDb_Entities_FavoriteUsers');

                $credentials = Model3_Auth::getCredentials();
                if ($credentials['type'] == DefaultDb_Entities_User::USER_DRIVER) {
                    if ($idRoutePoint > 0) {
                        //Se busca el punto
                        $point = $em->getRepository('DefaultDb_Entities_RoutePoint')->createQueryBuilder('rp')->select('IDENTITY(rp.point)')->Where("rp.id='" . $idRoutePoint . "'")->getQuery()->getResult();
                        // Buscamos al cliente relacionado con ese punto
                        $cliente = $em->getRepository('DefaultDb_Entities_BranchesUser')->createQueryBuilder('b')->select('IDENTITY(b.client)')->innerJoin("b.client","c")->Where("b.point='" . $point[0][1] . "' and c.type = 3")->orderBy('b.id','ASC')->getQuery()->getResult();
                        // Asignamos ese cliente a la variale $buyerId
                        $buyerId = $cliente[0][1];
                    } else {
                        $viewUser = new Model3_Session_Namespace('viewUser');
                        $buyerId = $viewUser->id;
                    }
                } else {
                    $buyerId = $credentials['id'];
                }
                
                $buyer = $em->find('DefaultDb_Entities_User', $buyerId);
                
                $pass = true;
                
                $listprducts = "";
                
                if (Model3_Auth::getCredentials('type') == DefaultDb_Entities_User::USER_DRIVER && $buyer && $idRoutePoint <= 0 ) {
                    $pass = false;
                    if ($this->getRequest()->isPost()) {
                        if (isset($post['pass'])) {
                            $pass = $post['pass'];
                            if ($buyer->getPassword() == md5($pass)) {
                                $pass = true;
                            }
                        }
                    }
                }
                
                if ($buyer && $pass) {
                    if ($cart)
                        $seller = $em->find('DefaultDb_Entities_User', $cart->getIdSeller());
                    else {
                        $post = $this->getRequest()->getPost();
                        if (isset($post['store'])) {
                            //$usersAdapter->findOneBy(array('id'=>$post['currentUser']));

                            $seller = $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $post['store']));
                        }
                    }
                                    
                    //$seller = $em->find('DefaultDb_Entities_User', $cart->getIdSeller());
                    //$post = $this->getRequest()->getPost();
                    $order = new DefaultDb_Entities_M3CommerceOrder();
                    $order->setBuyer($buyer);
                    $order->setSeller($seller);
                    $order->setCreationDate(new DateTime());
                    if (isset($post['comments'])) {
                        $order->setComments($post['comments']);
                    } else
                        $order->setComments('');
                    if (isset($post['pointBuyer'])) {
                        $pointRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
                        $pointBuyer = $pointRepos->find($post['pointBuyer']);
                        $order->setPointBuyer($pointBuyer->getPoint());
                    }
                    if ($cart) {
                        //Si tiene productos o carrito agregamos los productos a la orden
                        foreach ($cart->getItems() as $id => $item) {
                            $product = $em->find('DefaultDb_Entities_Product', $id);
                            if ($product && $product->getStock() >= $item['quantity']) {
                                if (isset($item['variant']) && count($item['variant']) > 0) {
                                    foreach ($item['variant'] as $idVariant => $itemVariant) {
                                        $productToOrder = new DefaultDb_Entities_M3CommerceProductToOrder();
                                        $productToOrder->setProduct($product);
                                        $variant = $em->find('DefaultDb_Entities_ProductVariants', $idVariant);
                                        $productToOrder->setVariant($variant);
                                        $variant->setStock($variant->getStock() - $itemVariant['quantity']);
                                        $productToOrder->setQuantity($itemVariant['quantity']);
                                        $productToOrder->setPrice($product->getPrice() * $itemVariant['quantity']);
                                        $order->addProduct($productToOrder);
                                        $listprducts .= "<span> -".$product->getName()."</span><br />";
                                        
                                    }
                                } else {

                                    $productToOrder = new DefaultDb_Entities_M3CommerceProductToOrder();
                                    $productToOrder->setProduct($product);
                                    $product->setStock($product->getStock() - $item['quantity']);
                                    $productToOrder->setQuantity($item['quantity']);
                                    $productToOrder->setPrice($product->getPrice() * $item['quantity']);
                                    $order->addProduct($productToOrder);
                                    $listprducts .= "<span> -".$product->getName()."</span><br />";
                                }
                               $this->view->error_stock = false;
                            } else {
                                $this->view->error_stock = true;
                                return;
                            }
                        }
                    }
                    
                    $em->persist($order);
                    $em->flush();
                    
                    $commerce->cart = false;
                   
                    $this->view->order = $order;
                    $this->view->orderId = $order->getId();
                    $this->view->sellerdat = "Puedes contactar al vendedor <br /> al correo: ".$seller->getMail()." y al Tel&eacute;fono: ".$seller->getPhone();
                    
                    $listp = "";
                    if($listprducts != ""){
                    	$listp .= "<span>La orden incluye los siguientes productos:</span><br />".$listprducts;
                    }
                    
                    $correo = "<html><body>
	            		<span>Hola ".$seller->getFirstName()." ".$seller->getLastName().",
	            		<br />
	            	 	revisa tus embarques en masdistribucion.com,
	            		<br >
	            		el cliente: ".$buyer->getFirstName()." ".$buyer->getLastName()."
	            		genero la orden: ".$order->getId()."<br />
	            		Puedes contactarlo al correo: ".$buyer->getMail()." y al Tel&eacute;fono: ".$buyer->getPhone()." </span><br />
						".$listp."
	            		</body></html>";
                    if($seller->getMail() != ""){
                    	$mailsend = $this->sendMail($correo,$buyer->getMail(),$seller->getMail(),"Aviso de pedido");
                    }
                    
                    $data = array(
                    		'title' => 'Nueva compra',
                    		'body' => 'Un cliente solicita una recolecci�n.',
                    );
                    
                    $this->sendPushNotification($seller->getToken(),  $data);
                    
                    if (isset($post['slcTipoMoneda']) || $buyer->getMoneda()->getId() ) {                        
                        $tipoMoneda = (isset($post['slcTipoMoneda']))?$post['slcTipoMoneda']: $buyer->getMoneda()->getId();
                        $montos = $this->getMontos($order, $em, $tipoMoneda);
                        $txtIdTipoPago = $post['txtIdTipoPago'];
                        $montoMoneda = $montos["totalMonedas"];
                        $monto = $montos["totalCreditos"];
                        error_log(" MONTOS  ".json_encode($montos));
                        
                        $txtCreditos = (isset($post['txtCreditos'])) ? $post['txtCreditos'] : 0;

                        $formaDebitar = 0;
                        $validaComit = TRUE;
                        //formaDebitar = 0 = DebitarCreditos
                        //formaDebitar = 1 = CongelarCreditos
                        //formaDebitar = 2 = CreditosNegativos
                        switch ($txtIdTipoPago) {
                            case "2": // Pago ahora con creditos
                                  $formaDebitar = 0;
                                if ($this->fncValidar($txtCreditos, $monto) === true) {
                                    $this->view->mensaje = "¡No cuenta con créditos suficientes para efectuar la compra!";
                                    $validaComit = FALSE;
                                } else {
                                    $this->view->mensaje = "Se realizo el pago con creditos. ";
                                    $paymentResult = $this->fncGuardarPago($em, $order, $txtIdTipoPago,$montoMoneda  , $monto , $formaDebitar);
                                }
                                break;
                            case "1": //Pago contra entrega
                                $formaDebitar = 4;
                                $formaDebitar = ($this->fncValidar($txtCreditos, $monto) === false) ? 1 : 2;
                                if ($formaDebitar === 1) {
                                    $this->view->mensaje = "¡Será congelada la cantidad de créditos que cubran el total de la adquisición!";
                                } else {
                                    $this->view->mensaje = "¡Al no contar con créditos suficientes, el pago generará créditos negativos!";
                                }
                                $formaDebitar = 1;
                                $paymentResult = $this->fncGuardarPago($em, $order, $txtIdTipoPago, $montoMoneda, $monto, $formaDebitar);

                                break;
                            
                            case "3": // Pago fuera de Mas Distribucion
                                $formaDebitar = 4;                                
                                $paymentResult = $this->fncGuardarPagoFuera($em, $order, $txtIdTipoPago, $monto, $formaDebitar);
                                break;
                        }
                        if( $validaComit){
                            $em->getConnection()->commit();
                        }else{
                              $em->getConnection()->rollback();
                        }
                    } else {
                        $em->getConnection()->rollback();
                    }

                    /* if (isset($post['slcTipoPago'])) {
                      $_SESSION['intIdTipoPagoCar'] = $post['slcTipoPago'];
                      $this->redirect('User/BackStore/viewEfectuarPago/id/'.$order->getId(), true,true);
                      } */
                }
            } catch (Exception $ex) {
                 $em->clear();
                 $em->close();
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
            }
        } else {
            header('Location:' . $this->view->url(array('controller' => 'Store', 'action' => 'index', 'module' => 'User')));
        }

        
        if($paymentResult)
        {
           
           $products = $order->getProducts();
           
           foreach($products as $producto){
                //Agregamos a favoritos al del cliente al vendedor
                $helperFavoriteUsers = new Helper_FavoriteUsers($this->getEntityManager('DefaultDb'));
                $helperFavoriteUsers->addFavorite($buyer->getId(), $seller->getId(), DefaultDb_Entities_FavoriteUsers::FAVORITE_SELLER,$producto->getProduct()->getId()); 
                //Agregamos como cliente favorito al provedor 
                $helperFavoriteUsers->addFavorite($seller->getId(), $buyer->getId(), DefaultDb_Entities_FavoriteUsers::FAVORITE_BUYER,$producto->getProduct()->getId());
           }
        }
        $this->view->payment_result = $paymentResult;
    }

    function getMontos($orden, $em, $moneda) {

        $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');

        $productos = $orden->getProducts();

        $totalCreditos = 0;
        $totalMonedas = 0;
        $montos = array();
        foreach ($productos as $productoCompra) {
            $producto = $productoCompra->getProduct();

            if ($productoCompra->getVariant()) {
                $productoNombre = $producto->getName() . ' - ' . $productoCompra->getVariant()->getDescription();
            } else {
                $productoNombre = $producto->getName();
            }

            $subtotalCreditos = $producto->getPriceCreditos() * $productoCompra->getQuantity();
            $totalCreditos += $subtotalCreditos;

            $subtotalMonedas = $pagosRepo->fncCalculaCreditos($moneda, $subtotalCreditos);
            $totalMonedas += $subtotalMonedas;

            /* $result[] = array(
              'cantidad' => $productoCompra->getQuantity(),
              'producto' => $productoNombre,
              'sku' => $producto->getSku(),
              'precioUnitario' => $producto->getPrice(),
              'precioSubtotal' => $subtotalCreditos,
              'precioMonedas' => $subtotalMonedas,
              'totalCreditos' => $totalCreditos,
              'totalMonedas' => $totalMonedas
              ); */
        }

        $montos["totalMonedas"] = $totalMonedas;
        $montos["totalCreditos"] = $totalCreditos;

        return $montos;
    }
    
    function fncGuardarPago( $em, $orden ,$tipoPago , $montoMoneda, $monto , $formaDebito ){
        $params = array();
        $result = false;
        try 
        {
            $tipoConcepto = $this->fncTipoConcepto($orden);
            $pagosJSON = array();
            $pagosJSON["usuario"] = $tipoConcepto["usuario"]; 
            $pagosJSON["cliente"] = $tipoConcepto["cliente"]; 
            $pagosJSON["orden"] = $orden->getId();
            $pagosJSON["montoCompra"] = $montoMoneda;
            $pagosJSON["montoCreditos"] = $monto;
            $pagosJSON["fecha"] = new DateTime( );
            $pagosJSON["estatus"] = ($tipoPago == 2) ? 1 : 2;
            $pagosJSON["compraCreditos"] = null;
            $pagosJSON["compraVenta"] = $orden;
            $pagosJSON["tipoConcepto"] = $tipoConcepto["tipoConcepto"];
            $pagosJSON["tipoDebito"] = $this->fncObtenerTipoDebito($formaDebito);
            $pagosJSON["descripcion"] = $tipoConcepto["descripcion"];
            $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');
            $pago = $pagosRepo->fncAgregarPago($pagosJSON);
            $result = true;
        } 
        catch (Exception $exc) 
        { 
            $result = false;
            $em->getConnection()->rollback();
            $pago["error"] = $this->logAndResolveException($exc,$params);
        } 
        return $result;
    }
    
    public function fncGuardarPagoFuera($em, $orden ,$tipoPago , $monto , $formaDebito) 
    {
        $params = array();
        $result = false;
        try 
        {
            $tipoConcepto = $this->fncTipoConcepto($orden);
            $pagosJSON = array();
            $pagosJSON["usuario"] = $tipoConcepto["usuario"]; 
            $pagosJSON["cliente"] = $tipoConcepto["cliente"]; 
            $pagosJSON["orden"] = $orden->getId();
            $pagosJSON["montoCompra"] = 0;
            $pagosJSON["montoCreditos"] = $monto;
            $pagosJSON["fecha"] = new DateTime( );
            $pagosJSON["estatus"] = ($tipoPago == 2) ? 1 : 2;
            $pagosJSON["compraCreditos"] = null;
            $pagosJSON["compraVenta"] = $orden;
            $pagosJSON["tipoConcepto"] = $tipoConcepto["tipoConcepto"];
            $pagosJSON["tipoDebito"] = $this->fncObtenerTipoDebito($formaDebito);
            $pagosJSON["descripcion"] = $tipoConcepto["descripcion"];

            $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');
            $pago = $pagosRepo->fncAgregarPago($pagosJSON);
            $result = true;
        } 
        catch (Exception $exc) 
        {
            $result = false;
            $em->getConnection()->rollback();
            $pago["error"] = $this->logAndResolveException($exc,$params);
        }  
        return $result;
    }
     /**
     * Metodo para verificar si es compra o es venta dependiendo del usuario logeado 
     * Se llama desde la funcion fncExistePagoAction linea 236
     */
    private function fncTipoConcepto($orden)
    {
        $VENTA = 3;
        $COMPRA = 4;
        
        $em = $this->getEntityManager('DefaultDb');
        $idUsuario = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        $usuario = $em->getRepository('DefaultDb_Entities_User')->find($idUsuario);
       
        if($usuario == $orden->getSeller() || $usuario == $orden->getBuyer())
            $cliente = ($usuario==$orden->getSeller()) ? $orden->getBuyer() : $orden->getSeller();
        else{
            $cliente = $orden->getBuyer();
            $usuario =  $orden->getSeller();
        }
        
        $concepto = ($usuario==$orden->getSeller()) ? $VENTA : $COMPRA;
        $tipoConcepto = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($concepto);
        $compra = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($COMPRA);
        $venta = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($VENTA);
        
        $datos = array();
        $datos["usuario"] = $usuario; 
        $datos["cliente"] = $cliente;
        $datos["tipoConcepto"] = $tipoConcepto;
        $datos["compra"] = $compra;
        $datos["venta"] = $venta;
        $datos["descripcion"] = ($concepto==$VENTA)? "Venta a ".$cliente->getCommercialName() 
                                : "Compra a ".$cliente->getCommercialName();
        
        return $datos;
    }

    public function reviewAction() {
        $commerce = new Model3_Session_Namespace('commerce');
        $cart = $commerce->cart;
        $neg = !NULL;
        $action = $this->getRequest()->getAction();
        $em = $this->getEntityManager('DefaultDb');
        $branchesRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
        $scheduleAdapter = $em->getRepository('DefaultDb_Entities_Schedule');
        $userRepos = $em->getRepository('DefaultDb_Entities_User');
        $idSeller = "";
        $credentials = Model3_Auth::getCredentials();
        if ($credentials['type'] == DefaultDb_Entities_User::USER_DRIVER) {
            $viewUser = new Model3_Session_Namespace('viewUser');
            $buyer = $userRepos->find($viewUser->id);
        } else {
            $buyer = $userRepos->find($credentials['id']);
        }
        if ($cart) {
            $idSeller = $cart->getIdSeller();
            $tienda = $em->find('DefaultDb_Entities_User', $cart->getIdSeller());
            $this->view->tiendaSelected = $tienda;
            $branchesBuyer = $branchesRepos->findBy(array('client' => $buyer));
            $branchesSeller = $branchesRepos->findBy(array('client' => $idSeller));
            $this->view->branchesBuyer = $branchesBuyer;
            $this->view->branchesSeller = $branchesSeller;
        }
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $this->view->comments = $post['comment'];
            $tienda = $em->find('DefaultDb_Entities_User', $post['store']);
            $this->view->tiendaSelected = $tienda;
            $idSeller = $tienda->getId();
            $branchesBuyer = $branchesRepos->findBy(array('client' => $buyer));
            $branchesSeller = $branchesRepos->findBy(array('client' => $idSeller));
            $this->view->branchesBuyer = $branchesBuyer;
            $this->view->branchesSeller = $branchesSeller;
        } else {
            if (!$cart)
                header('Location:' . $this->view->url(array('controller' => 'Store', 'action' => 'index', 'module' => 'User')));
        }
        if (Model3_Auth::getCredentials('type') == DefaultDb_Entities_User::USER_DRIVER) {
            $em = $this->getEntityManager('DefaultDb');
            $viewUser = new Model3_Session_Namespace('viewUser');
            $this->view->viewUser = $em->find('DefaultDb_Entities_User', $viewUser->id);
        }
        $this->view->getJsManager()->addJsVar('urlGetSchedules', '\'' . $this->view->url(array('module' => '', 'controller' => 'Ajax_UserShipping', 'action' => 'getSchedulesByOrigin')) . '\'');
        $this->view->getJsManager()->addJsVar('urlSelectSchedule', '\'' . $this->view->url(array('module' => '', 'controller' => 'Ajax_UserShipping', 'action' => 'calculateShippingPerRoute')) . '\'');
        $this->view->getJsManager()->addJsVar('idSeller', '\'' . $idSeller . '\'');
        $this->view->action = $action;
    }

    public function dmReviewAction() {
        //$this->initialize();
        $this->view->getCssManager()->addCss('blueprint/screen.css');
        $this->view->getJsManager()->addJs('templates/dineromail' . JS_POSFIX . '.js');
        $this->view->nomFix = $this->getParam(0);
        $siteAdapter = new Model_Sitios();
        $helper = new Helper_DisplayHelper();
        $this->view->reviewCart = true;
        $this->view->method = false;

        if ($this->view->nomFix == '') {
            $sitio = $helper->getSiteByDomain();
        } else
            $sitio = $siteAdapter->getSiteByNameOnly($this->view->nomFix, SELECTOR);

        $this->prepareView();
        $tiendaAdapter = new Data_Tiendas();
        $this->view->configTienda = $tiendaAdapter->findOne('id_sitio', $sitio['id']);
        $helperPaypal = new Helper_paypalResponse($sitio['id']);
        if (PRODUCCION)
            $this->view->url = $sitio['dominio'] != '' ? $sitio['dominio'] . '/confirmDmOrder' : 'http://' . $sitio['nombre_fix'] . '.storesfactory.com/confirmDmOrder/' . $sitio['nombre_fix'];
        else
            $this->view->url = 'http://localhost/storesfactory/public/Display/confirmDmOrder/' . $sitio['nombre_fix'];

        if ($this->isPost()) {
            $post = $this->getPost();
            $this->view->response = $helperPaypal->buildEmptyResponse();
            $this->view->method = $post['method'];
        }
    }

    private function prepareView() {
        $action = $this->view->action;
        $this->view->idUsuario = $this->view->showCart = false;

        foreach ($widgets as $widget) {
            if ($widget['tipo'] == WIDGET_CATALOGS) {
                $this->view->getCssManager()->addCss('tienda' . CSS_POSFIX . '.css');
            }

            if ($widget['tipo'] == TRADUCTOR)
                $this->view->loadGoogleTranslateAPI = true;
        }

        $paginasAdapter = new Model_VistaPaginas();
        $page = $paginasAdapter->getPageByNameBySitio($this->_nomFixPage, $this->_sitio['id']);
        if ($page && $page->categoria == PAGINA_TIENDA) {
            $this->view->linkNav = '/' . $page->nombre_fix;
            $this->view->showCart = true;
            $idSitio = $this->_sitio['id'];
            $this->view->idSitio = $idSitio;
            $this->view->nomFix = $this->_sitio['nombre_fix'];
            $this->view->nomPageFix = $this->_nomFixPage;
            $this->view->tipo_template = $this->_sitio['tipo_template'];
            $this->view->template = $this->_sitio['template'];

            //consultar productos para imprimirlos
            $this->getProductsInitials($idSitio);
            //$this->view->productos = $productos;
            //var_dump($productos);


            $tagsGroups = array();
            //var_dump($productos);
            $exceptTagsName = '';

            switch ($tienda->fuente_productos_iniciales) {
                case Data_Catalogos::INICIO_CATALOGO:
                    $tagsGroups = $this->getTagGroupNames($idSitio); //Da todos los grupos
                    $tagsGroups = $this->cleanGroups($tagsGroups, Data_Catalogos::INICIO_CATALOGO, $idSitio); //limpia dependiendo del catalogo
                    break;
                case Data_Catalogos::INICIO_ETIQUETAS:
                    $exceptTagsName = $this->getInitTagsName($tienda);
                    $this->view->tagsIni = $tienda->valor_productos_iniciales;

                    $tagsGroups = $this->getTagGroupNamesSelected($idSitio, $exceptTagsName); //debe poder tomar todos execto los que ya tomo y quitando los que ya
                    $tagsGroups = $this->cleanGroups($tagsGroups, Data_Catalogos::INICIO_ETIQUETAS, $idSitio, $exceptTagsName);

                    break;
                case Data_Catalogos::INICIO_DESTACADOS:
                    $tagsGroups = $this->getTagGroupsDestacados($idSitio);
                    $tagsGroups = $this->cleanGroups($tagsGroups, Data_Catalogos::INICIO_DESTACADOS, $idSitio);
                    break;
            }
            ////obtener las etiquetas de cada grupo y almacenarlas en un arreglo

            $this->view->tagsNameSelected = $exceptTagsName;
            $this->view->tagsGroups = $tagsGroups;
            $this->view->getCssManager()->addCss('tienda' . CSS_POSFIX . '.css');
        }

        //se inicializan los valores para el template Avanzado
        if ($this->_sitio['tipo_template'] == 2) {
            $templateHelper = new Helper_TemplateAvanzado();
            $layout = '';
            if ($action != 'viewroomcart' && $action != 'viewcart' && $action != 'confirmorder' && $action != 'review' && $action != 'confirmDmOrder') {
                //if( $this->view->pagina['categoria'] != PAGINA_TIENDA )
                $layout = $this->view->pagina['layout'];
            }

            $toInclude = $templateHelper->initAdvancedTemplate($this->_sitio, $layout);
            $this->view->css = $toInclude['css'];
            $this->view->js = $toInclude['js'];

            //Estilos para los sitios de template con plantilla propia,
            //se agregan los estilos de contacto y de comentarios y otros...
            $this->cssTemplateAvanzado($toInclude);
        } else {
            /**
             * Esta seccion se agrega para poder agregar los archivos css y js del modo avanzado al modo personalizado
             */
            $templateHelper = new Helper_TemplateAvanzado();
            $toInclude = $templateHelper->initAdvancedTemplate($this->_sitio, '');
            $this->view->css = $toInclude['css'];
            $this->view->js = $toInclude['js'];

            $this->setLayoutToTemplate();
        }

        //Si es una accion de tienda no se llaman los elementos
        if ($action != 'viewroomcart' && $action != 'viewcart' && $action != 'confirmorder' && $action != 'confirmDmOrder' /* && $action != 'billing' */ && $action != 'review') {
            if ($this->view->pagina) {
                //obtenemos los elementos
                if (!$this->view->elementMode)
                    $this->getElements();
                else
                    $this->getSingleElement();
            }
        }
    }

    /**
     * Crea un elemento temporal con id de elemento = -1
     * @param int $idSitio
     * @param int $tipoElemento
     * @param string $contenido Para el elemento catalogo es el tipo de vista del catalogo(lista, o 
     * @param int $id
     * @return array Arreglo asociativo con los campos 'contenido', 'id'=>-1
     */
    protected function createTemporalElement($idSitio, $tipoElemento, $contenido, $id) {
        return Helper_Sites::createTemporalElement($idSitio, $tipoElemento, $contenido, $id);
    }

    /**
     * Regresa los productos que seran mostrados al inicio de la tienda
     * segun la confoguracion definida por el usuario
     * @param int $idSitio
     * @return array|false
     */
    private function getProductsInitials($idSitio) {
        $tiendasAdapter = new Data_Tiendas();
        $catalogosAdapter = new Data_Catalogos();
        $productosAdapter = new Data_Productos();
        $tienda = $tiendasAdapter->getStoreForASite($idSitio);
        $numCatalog = $catalogosAdapter->getNumCatalogbySitioId($idSitio);
        $numProducts = $productosAdapter->getNumProductsBySitioId($idSitio, 1);

        if (is_array($tienda) && count($tienda) > 0 && $numCatalog > 0 && $numProducts > 0) {
            $tienda = $tienda[0];
            $productosAdapter = new Data_Productos();
            $this->view->vistaCatalogo = false;
            $this->view->idCatalogo = false;

            switch ($tienda->fuente_productos_iniciales) {
                case Data_Catalogos::INICIO_CATALOGO:
                    $idCatalogo = $tienda->valor_productos_iniciales;
                    $this->view->vistaCatalogo = true;
                    $contenido = array('tipo_vista' => 1, 'num_renglones' => '', 'num_columnas' => '', 'num_productos_lista' => '', 'num_productos_slide' => '');
                    $this->view->catalogElement = $this->createTemporalElement($idSitio, ETIPO_CATALOGO, $contenido, $idCatalogo);
                    $this->view->idCatalogo = $idCatalogo;
                    break;
                case Data_Catalogos::INICIO_ETIQUETAS:
                    if (isset($post['edicion']) && $post['edicion'] == 1) {
                        $this->view->edicion = true;
                    }
                    $this->view->vistaCatalogo = true;
                    $contenido = array('tipo_vista' => 1, 'num_renglones' => '', 'num_columnas' => '', 'num_productos_lista' => '', 'num_productos_slide' => '');
                    $this->view->catalogElement = $this->createTemporalElement($idSitio, ETIPO_CATALOGO, $contenido, Data_Catalogos::INICIO_ETIQUETAS);
                    break;
                case Data_Catalogos::INICIO_DESTACADOS:
                    $this->view->vistaCatalogo = true;
                    $contenido = array('tipo_vista' => 1, 'num_renglones' => '', 'num_columnas' => '', 'num_productos_lista' => '', 'num_productos_slide' => '');
                    $this->view->catalogElement = $this->createTemporalElement($idSitio, ETIPO_CATALOGO, $contenido, Data_Catalogos::CATALOGO_DESTACADOS);
                    break;
            }
        }
    }

    public function getInitTagsName($tienda) {
        $exceptTagsName = $tienda->valor_productos_iniciales;
        $exceptTagsName = explode(',', $exceptTagsName);
        foreach ($exceptTagsName as $k => $tagName) {
            if (strlen($tagName) <= 0)
                unset($exceptTagsName[$k]);
            else
                $exceptTagsName[$k] = '"' . $tagName . '"';
        }
        return $exceptTagsName;
    }

    private function cleanGroups($tagsGroups, $tipoCatalogo, $idSitio, $exceptTagsName = '') {
        if ($tipoCatalogo == Data_Catalogos::INICIO_ETIQUETAS) {
            $productosAdapter = new Data_Productos();
            $where = '';
            if (is_array($exceptTagsName) && count($exceptTagsName) > 0)
                $where .= ' t.valor IN ( ' . implode(',', $exceptTagsName) . ' )';
            $productos = $productosAdapter->getProductsByTagsValue($idSitio, $where);
        }
        foreach ($tagsGroups as $key => $tagGroup) {
            $tagsAdapter = new Data_Tags();
            $idGrupo = $tagGroup['id'];
            switch ($tipoCatalogo) {
                case Data_Catalogos::INICIO_CATALOGO:
                    $tags = $tagsAdapter->getTagsByGroupIdAndCatalogId($idGrupo, $this->view->idCatalogo, array(), FETCH_ASSOC);
                    break;
                case Data_Catalogos::INICIO_ETIQUETAS:

                    $namesProductos = array();
                    foreach ($productos as $i => $producto)
                        $namesProductos[$i] = '"' . $producto->nombre . '"';
                    $tags = $tagsAdapter->getTagsByGroupExceptTagsNames($idGrupo, $exceptTagsName, $namesProductos, FETCH_ASSOC);
                    break;
                case Data_Catalogos::INICIO_DESTACADOS:
                    $tags = $tagsAdapter->getTagsByGroupDestacados($idGrupo, FETCH_ASSOC);
                    break;
            }
            if (is_array($tags) && count($tags) > 0) {
                $tagsGroups[$key]['tags'] = $tags;
            } else {
                unset($tagsGroups[$key]);
            }
        }
        return $tagsGroups;
    }

    private function getTagGroupNames($idSitio) {
        $groupsTagsAdapter = new Data_TagsGroups();
        $tagsGroups = $groupsTagsAdapter->getNombreGrupo($idSitio, null, FETCH_ASSOC);
        return $tagsGroups;
    }

    private function getTagGroupsDestacados($idSitio) {
        $groupsTagsAdapter = new Data_TagsGroups();
        $tagsGroups = $groupsTagsAdapter->getGroupsDestacados($idSitio, FETCH_ASSOC);
        //$tagsGroups=$tagAdapter->getGroupsSelectedByTags($exceptTagsName,$idSitio);
        return $tagsGroups;
    }

    private function getTagGroupNamesSelected($idSitio, $tagsIni) {
        //$exceptTagsName=$tagsExcept;
        $tagAdapter = new Data_Tags();
        $tagsGroups = $tagAdapter->getGroupsSelectedByTags($tagsIni, $idSitio);
        return $tagsGroups;
    }

    public function viewProductAction() {
        $this->view->product = false;
        $idProducto = $this->getRequest()->getParam('id');
        if ($idProducto) {
            $em = $this->getEntityManager('DefaultDb');
            $product = $em->find('DefaultDb_Entities_Product', $idProducto);
            if ($product) {
                $this->view->product = $product;
                $catalog = $product->getCatalog();
                $this->view->aditional_title = $product->getname() . ' :: ' . $catalog->getTitle();
                $this->view->alternative_description = htmlentities(strip_tags($product->getDescription()));
                $this->view->catalog = $catalog;
                $this->view->user = $catalog->getClient();
                $this->view->category = $this->view->user->getCategory();
            }
        }
    }

    private function cssTemplateAvanzado($toInclude) {
        $this->view->getCssManager()->addCss('styleTemplate2' . CSS_POSFIX . '.css');
        $this->view->htmlLayout = $toInclude['html'];
        $this->view->setTemplate('TemplateAvanzado');
    }

    public function getCatalog($idSitio, $index) {
        $idCatalog = $this->getParam($index);

        /**
         * Esta validacion es temporal, arreglar el .htaccess para formar correctamente las url's
         * en este momento genera /viewCatalog/nomfix/viewCatalog/idProducto
         */
        if ($idCatalog == 'viewCatalog')
            $idCatalog = $this->getParam($index + 1);
        //verificar que existe el catalogo
        $catalog = false;
        if ($idCatalog) {
            $CatalogAdapter = new Data_Catalogos();
            $catalog = $CatalogAdapter->getCatalogByIdSitioAndCatalog($idSitio, $idCatalog);

            if ($catalog) {
                $contenido = array('tipo_vista' => 1, 'num_renglones' => '', 'num_columnas' => '', 'num_productos_lista' => '', 'num_productos_slide' => '');
                $this->view->catalogElement = $this->createTemporalElement($idSitio, ETIPO_CATALOGO, $contenido, $idCatalog);
            } else {
                $this->view->catalogElement = false;
            }
        }
        return $catalog;
    }

    /**
     * Inicializa las variables ocupadas en la vista con valores predeterminados.
     * Posteriormente se dan valores dependiendo de la accion en la que nos encontremos.
     */
    protected function setViewVars() {
        $helper = new Helper_DisplayHelper();
        //definimos la accion por la que ingresamos, en produccion no será visible por las reglas del htaccess (index o customDomain)
        $this->view->action = $this->_request->getAction();
        //Indica que no permitiremos la edicion al llamar a los elementos
        $this->view->edition = 0;
        // indica si desplegaremos unicamente un elemento dado
        $this->view->elementMode = $helper->isElementMode($this->getParam(2));
        //Indicará si en la pagina hay elementos galeria
        $this->view->galeria = 0;
        //indica si desplegamos la tienda
        $this->view->tienda = false;
        //Indica si debemos mostrar el carrito de compra
        $this->view->viewCart = false;
        //Indica si desplegaremos los detalles del carrito de compra
        $this->view->reviewCart = false;
        //Indica si estamos en el paso de confirmacion de ordenes
        $this->view->confirmorder = false;
        //Indica si la pagina inicial es de tipo tienda
        $this->view->inicialPageIsStore = false;
        //Si no tiene pagina para los catalogos
        $this->view->nameFixStore = "";
        //define si se muestra o no el menu de cnofiguracion
        $this->view->menuConfig = false;
        //variable para los permisos de edicion
        $this->view->permisos = 0;
        //contendrá los widgets
        $this->view->widgets = array();
        //indica si existe un elemento mapa
        $this->view->addMap = false;
        //ancho default
        $this->view->width1 = '700px';
        //clase default
        $this->view->clase1 = '';
        //indica si estamos en modo preview
        $this->view->preview = false;
        //title complementario para productos o catalogos
        $this->view->aditional_title = false;
        //descripcion alternativa para las vistas de productos
        $this->view->alternative_description = false;
    }

    /**
     * Añade los css necesarios
     */
    protected function setCssFiles() {
        $this->view->getCssManager()->addCss('cssgallery/css/galleriffic-2.css');
        $this->view->getCssManager()->addCss('cssgallery/css/galleriffic-5.css');

        $this->view->getJsManager()->addJs('jquery.opacityrollover.js');
        //los siguientes se encontraban en la funcion initialize llamada multiples veces
        $this->view->getCssManager()->addCss('fullcalendar.css');
        $this->view->getCssManager()->addCss('style-1.css');
        $this->view->getCssManager()->addCss('twitter.css');
        $this->view->getCssManager()->addCss('sexylightbox.css');
        $this->view->getCssManager()->addCss('menu-1.css');
        $this->view->getCssManager()->addCss(JQUERY_UI_CSS);
        $this->view->getCssManager()->addCss('widgets-1.css');
        $this->view->getCssManager()->addCss('captify.css');
        $this->view->getCssManager()->addCss('styleSocial-1.css');
        $this->view->getCssManager()->addCss('catalog-1.css');
        $this->view->getCssManager()->addCss('productsviews-1.css');
        $this->view->getCssManager()->addCss('cloudZoom/cloud-zoom.css');
        $this->view->getCssManager()->addCss('agilecarousel/agile_carousel.css');
        $this->view->getCssManager()->addCss('reservations/reservations.css');
    }

    /**
     * Añade los js necesarios. Previamente llamados desde la function initialize
     */
    protected function setJsFiles($element = '') {
        switch ($element) {
            case '':
                $this->view->getJsManager()->addJs(JQUERY_UI_SCRIPT);
                $this->view->getJsManager()->addJs('captify.js');
                $this->view->getJsManager()->addJs('jquery.tools.min.js');
                $this->view->getJsManager()->addJs('jquery.timers-1.1.2.js');
                $this->view->getJsManager()->addJs('templates/display' . JS_POSFIX . '.js');
                $this->view->getJsManager()->addJs('jquery.validate.js');
                $this->view->getJsManager()->addJs('templates/comentarios' . JS_POSFIX . '.js');
                $this->view->getJsManager()->addJs('templates/widgets' . JS_POSFIX . '.js');
                $this->view->getJsManager()->addJs('fullcalendar.js');
                $this->view->getJsManager()->addJs('sexylightbox.v2.3.jquery.js');
                $this->view->getJsManager()->addJs('jquery.tooltip.js');
                $this->view->getJsManager()->addJs('templates/ecommerce' . JS_POSFIX . '.js');
                $this->view->getJsManager()->addJs('cloudZoom/cloud-zoom.1.0.2.js');
                $this->view->getJsManager()->addJs('templates/productsviews' . JS_POSFIX . '.js');
                $this->view->getJsManager()->addJs('jquery.form.js');

                $siteAdapter = new Model_Sitios();
                $site = $siteAdapter->getSiteById($this->_sitio['id']);
                if ($site['categoria'] == HOTEL) {
                    $this->view->getJsManager()->addJs('Reservaciones/datepicker.js');
                    $this->view->getJsManager()->addJs('Reservaciones/validateDatesReservations.js');
                }
                break;
            case ETIPO_MAP:
            case ETIPO_PRODUCTO:
                $this->view->getJsManager()->addJs('googleMaps.js');
                break;
            case ETIPO_GALLERY:
                $this->view->getJsManager()->addJs('jquery.galleriffic.js');
                break;
            case ElementsController::ETIPO_SLIDE:
                $this->view->getJsManager()->addJs('agilecarousel/agile_carousel.alpha.js');
                break;
        }
    }

    /**
     * En caso de no tener el nombre de la pagina,
     * se infiere que la pagina que se muestra es la de inicio                     
     */
    protected function setInitialPageData() {
        $siteAdapter = new Model_Sitios();

        if (strlen($this->_nomFixPage) <= 0) {
            $this->view->pagina = $siteAdapter->getPage($this->_sitio['id'], $this->_sitio['id_pagina_inicio'], SELECTOR);
            $this->_nomFixPage = $this->view->pagina['nombre_fix'];
        } else {
            $this->view->pagina = $siteAdapter->getPageByName($this->_sitio['id'], $this->_nomFixPage);
        }

        if ($this->view->pagina && $this->view->pagina['categoria'] == PAGINA_TIENDA) {
            $this->view->inicialPageIsStore = true;
        }

        //Obteniendo opciones de la pagina                
        $this->view->currentPag = $this->view->pagina;

        $namePagTienda = $siteAdapter->getPageTiendaByIdSitio($this->_sitio['id'], SELECTOR);

        if ($namePagTienda)
            $this->view->nameFixStore = $namePagTienda->nombre_fix;

        $this->view->idSitio = $this->_sitio['id'];
        $this->view->moderacion = $this->_sitio['moderacion'];
        $this->view->template = $this->_sitio['template'];
        $this->view->datos = $this->_sitio;
        $this->view->comentariosDefault = (int) $this->_sitio['comentarios'];
    }

    /**
     * Pone en uno o cero la bandera que define si se carga o no blueprint
     * @param int $tipo_template 
     */
    protected function needBlueprint($tipo_template) {
        if ($tipo_template != 2) {
            //$this->initialize();
            $templateSiteHelper = new Helper_TemplateSites();
            $this->view->loadBlueprint = $tipo_template == 0 ? $templateSiteHelper->blueprint(true) : $templateSiteHelper->blueprint();
        }
    }

    /**
     * Define las clases y variables que dan el ancho de los elementos
     * @param array $sitio 
     */
    protected function setWidthVars($sitio) {
        $templateSiteHelper = new Helper_TemplateSites();
        $templateSiteHelper->InitTemplate($sitio);
        $this->view->clase1 = $templateSiteHelper->siteClass();
        $this->view->width1 = $templateSiteHelper->width();
    }

    /**
     * Obtiene las páginas y subpáginas del sitio
     */
    protected function setSitePages() {
        $siteAdapter = new Model_Sitios();
        $this->view->paginas = $siteAdapter->getPages($this->_sitio['id'], SELECTOR);

        if ($this->view->pagina) {
            $this->view->hijos = $siteAdapter->getSons($this->_sitio['id'], $this->view->pagina['id'], SELECTOR);
            $this->view->hermanos = $siteAdapter->getBrothers($this->view->pagina['id_padre'], $this->_sitio['id'], SELECTOR);
            $this->view->idPagina = $this->view->pagina['id'];
        }
    }

    private function setLayoutToTemplate() {
        $this->iniTemplate('');
        $paginasAdapter = new Model_VistaPaginas();
        $page = $paginasAdapter->getPageByNameBySitio($this->_nomFixPage, $this->_sitio['id']);
        if (($page && $page->categoria == PAGINA_TIENDA ) || $this->_request->getAction() == 'viewproduct' || $this->_request->getAction() == 'viewCatalog') {
            $useOwnBase = false;

            if ($this->_sitio['tipo_template'] == 1) {
                $t = $this->_sitio['template'];

                if (($t >= 13 && $t <= 22) || $t == 7 || $t == 24 || ($t >= 26 && $t <= 31)) {
                    $useOwnBase = true;
                }
            }

            if ((($this->_sitio['tipo_template'] == 1) && $useOwnBase == false))
                $this->view->setTemplate('TemplateTiendaStores');
            else if ((($this->_sitio['tipo_template'] == 0) && ($this->_sitio['template'] == 4)))
                $this->view->setTemplate('TemplateTienda');
        }
    }

    public function checkPassWordPageAjaxAction() {
        $this->view->setUseTemplate(false);
        $siteAdapter = new Model_Sitios();
        $correct = false;

        if ($this->isPost()) {
            $post = $this->getPost();
            $typedPassword = $post['typedPass'];
            $pageId = $post['idPage'];
            $siteId = $post['idSite'];
            $this->view->pagina = $siteAdapter->getPage($siteId, $pageId, SELECTOR);

            if ($typedPassword != '') {
                $password = base64_decode($this->view->pagina['password']);
                if ($password == $typedPassword) {
                    $correct = true;
                    $this->getElements(); /* Se obtienen los elementos */
                }
            }

            $this->view->correct = $correct;
        }
    }

    public function viewRoomAction() {
//        $this->view->elementView = 1;
//        $this->view->useGPlusButton = true;
//        $this->view->useTwitterWidgets = true;
        $sesion = new Model3_Session_Namespace('reservaciones');
        $siteAdapter = new Model_Sitios();
        $fechaInicio = $sesion->fechaInicio;
        $fechaSalida = $sesion->fechaSalida;

        $imagesRoomsAvaliablesAdapter = new Model_Reservaciones_ImagenesHabitaciones();
        $roomsAvaliableAdapter = new Helper_Reservaciones_Reservations();
        $roomsAdapter = new Model_Reservaciones_Habitaciones();

        $imagesRoomsAvaliables = $imagesRoomsAvaliablesAdapter->getImageByIdSitio($this->_sitio['id']);
        $roomsAvailable = $roomsAdapter->getRoomsByIdSitio($this->_sitio['id']);

        //var_dump($imagesRoomsAvaliables);
        $this->view->imagesRoomAvaliable = $imagesRoomsAvaliables;
        $this->view->roomAvaliable = $roomsAvailable;
        $this->setWidthVars($this->_sitio);
        //$this->view->loadBlueprint = true;
        $this->needBlueprint($this->_sitio['tipo_template']);
        $this->view->getJsManager()->addJs('googleMaps.js');

        if ($this->isPost()) {
            $post = $this->getPost();
            $fechaInicio = $post['txtStartDate'];
            $fechaSalida = $post['txtEndDate'];


            $sesion->fechaInicio = $fechaInicio;
            $sesion->fechaSalida = $fechaSalida;
            $this->view->roomReserved = $roomReserved = $roomsAdapter->getRoomsReservedByIdSitio($this->_sitio['id'], $fechaInicio, $fechaSalida);

            //   var_dump($roomReserved);
        } else if ($fechaInicio != null && $fechaSalida != null) {
            $this->view->roomReserved = $roomReserved = $roomsAdapter->getRoomsReservedByIdSitio($this->_sitio['id'], $fechaInicio, $fechaSalida);
        } else {
            //var_dump('hol');
            $newUrl = 'Display/index/' . $this->view->nomFix . '/' . $this->_nomFixPage;
            $this->redirect($newUrl);
        }

        $this->prepareViewRoom();
    }

    public function prepareViewRoom() {
        $this->view->idUsuario = $this->view->showCart = false;
        $siteAdapter = new Model_Sitios();
        $tiendasAdapter = new Model_Reservaciones();
        $helper = new Helper_DisplayHelper();

        $this->view->getCssManager()->addCss('tienda' . CSS_POSFIX . '.css');
        $tienda = $tiendasAdapter->findOne('id_sitio', $this->_sitio['id']);
        $this->view->tienda = $tienda;
        $this->view->idUsuario = $this->_sitio['id_usuario'];
        $this->setLinks($this->_sitio);
        $this->view->showCart = true;
        $this->getSingleProduct($this->_sitio['id'], $this->_baseIndex);
        //obtenemos el contenido de la pag
        $this->getPageContent($this->_nomFixPage, $this->_sitio);
        //Obtenemos las paginas del sitio
        $this->setSitePages();

        $paginasAdapter = new Model_VistaPaginas();
        $idSitio = $this->_sitio['id'];
        $this->view->tipo_template = $this->_sitio['tipo_template'];

        if ($this->_sitio['tipo_template'] == 2) {
            $templateHelper = new Helper_TemplateAvanzado();
            $layout = '';
            $toInclude = $templateHelper->initAdvancedTemplate($this->_sitio, $layout);
            $this->view->css = $toInclude['css'];
            $this->view->js = $toInclude['js'];
            //Estilos para los sitios de template con plantilla propia,
            //se agregan los estilos de contacto y de comentarios y otros...
            $this->cssTemplateAvanzado($toInclude);
        } else {
            $this->setLayoutToTemplate();
        }
    }

    public function confirmRoomAction() {
        $siteAdapter = new Model_Sitios();
        $this->setWidthVars($this->_sitio);
        //$this->view->loadBlueprint = true;
        if ($this->isPost()) {
            $post = $this->getPost();
            $idRoom = $post['idRoom'];
        } else {
            $idRoom = $this->getParam(1);
        }
        $roomsAdapter = new Helper_Reservaciones_Reservations();
        $room = $roomsAdapter->getRoomByIdRoom($idRoom);
        $this->view->room = $room;
        $this->view->item = null;
        if ($this->getParams() > 1) {
            $this->view->item = $this->getParam(2);
        }
        $this->needBlueprint($this->_sitio['tipo_template']);
        $this->view->getJsManager()->addJs('googleMaps.js');
        $this->prepareConfirmRoom();
    }

    public function prepareConfirmRoom() {
        $this->view->idUsuario = $this->view->showCart = false;
        $siteAdapter = new Model_Sitios();
        $helper = new Helper_DisplayHelper();

        $this->view->getCssManager()->addCss('tienda' . CSS_POSFIX . '.css');
        $this->view->idUsuario = $this->_sitio['id_usuario'];
        $this->setLinks($this->_sitio);
        $this->view->showCart = true;
        $this->getSingleProduct($this->_sitio['id'], $this->_baseIndex);
        //obtenemos el contenido de la pag
        $this->getPageContent($this->_nomFixPage, $this->_sitio);
        //Obtenemos las paginas del sitio
        $this->setSitePages();

        $paginasAdapter = new Model_VistaPaginas();
        $idSitio = $this->_sitio['id'];
        $this->view->tipo_template = $this->_sitio['tipo_template'];

        if ($this->_sitio['tipo_template'] == 2) {
            $templateHelper = new Helper_TemplateAvanzado();
            $layout = '';
            $toInclude = $templateHelper->initAdvancedTemplate($this->_sitio, $layout);
            $this->view->css = $toInclude['css'];
            $this->view->js = $toInclude['js'];
            //Estilos para los sitios de template con plantilla propia,
            //se agregan los estilos de contacto y de comentarios y otros...
            $this->cssTemplateAvanzado($toInclude);
        } else {
            $this->setLayoutToTemplate();
        }
    }

    public function carReservationRoomAction() {
        $siteAdapter = new Model_Sitios();
        $this->setWidthVars($this->_sitio);
        //$this->view->loadBlueprint = true;

        $roomsAdapter = new Helper_Reservaciones_Reservations();


        $this->needBlueprint($this->_sitio['tipo_template']);
        $this->view->getJsManager()->addJs('googleMaps.js');
        $this->prepareCarReservationRoom();
    }

    public function prepareCarReservationRoom() {
        $this->view->idUsuario = $this->view->showCart = false;
        $siteAdapter = new Model_Sitios();
        $tiendasAdapter = new Data_Tiendas();
        $helper = new Helper_DisplayHelper();

        $this->view->getCssManager()->addCss('tienda' . CSS_POSFIX . '.css');
        $tienda = $tiendasAdapter->findOne('id_sitio', $this->_sitio['id']);
        $this->view->idUsuario = $this->_sitio['id_usuario'];
        $this->setLinks($this->_sitio);
        $this->view->showCart = true;
        $this->getSingleProduct($this->_sitio['id'], $this->_baseIndex);
        //obtenemos el contenido de la pag
        $this->getPageContent($this->_nomFixPage, $this->_sitio);
        //Obtenemos las paginas del sitio
        $this->setSitePages();

        $paginasAdapter = new Model_VistaPaginas();
        $idSitio = $this->_sitio['id'];
        $this->view->tipo_template = $this->_sitio['tipo_template'];

        if ($this->_sitio['tipo_template'] == 2) {
            $templateHelper = new Helper_TemplateAvanzado();
            $layout = '';
            $toInclude = $templateHelper->initAdvancedTemplate($this->_sitio, $layout);
            $this->view->css = $toInclude['css'];
            $this->view->js = $toInclude['js'];
            //Estilos para los sitios de template con plantilla propia,
            //se agregan los estilos de contacto y de comentarios y otros...
            $this->cssTemplateAvanzado($toInclude);
        } else {
            $this->setLayoutToTemplate();
        }
    }

    public function categoryAction() {
        $categoryId = $this->getRequest()->getParam('id');
        $sessionUserId = Model3_Auth::getCredentials('id');
        $em = $this->getEntityManager('DefaultDb');
        $categoryRepos = $em->getRepository('DefaultDb_Entities_Category');
        $branchesRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
        $pointRepos = $em->getRepository('DefaultDb_Entities_Point');
        $this->view->category = $categoryRepos->find($categoryId);
        $this->view->userId = $sessionUserId;
        
        $this->view->allstores =$pointRepos->getUnuserPoints($categoryId);//OBTENER PUNTOS SIN USUARIO ASIGNADO
        
        $companyId = $this->getRequest()->getParam('id');
        $this->view->user = $em->find('DefaultDb_Entities_User', $companyId);

        $this->view->getJsManager()->addJs('view/scripts/User/Store/user.js');
        $this->view->getCssManager()->addCss('view/scripts/User/Store/user.css');
        $this->view->getJsManager()->addJsVar('urlAddFavorite', '\'' . $this->view->url(array('module' => '', 'controller' => 'Ajax_Favorite', 'action' => 'addFavorite')) . '\'');
        $this->view->getJsManager()->addJsVar('urlRemoveFavorite', '\'' . $this->view->url(array('module' => '', 'controller' => 'Ajax_Favorite', 'action' => 'removeFavorite')) . '\'');
        if ($this->view->user != null) {
            $this->view->getJsManager()->addJsVar('commercialName', '\'' . $this->view->user->getCommercialName() . '\'');
        }
        $this->view->getJsManager()->addJsVar('urlGetBranchesUser','\'' . $this->view->url(array('module' => '', 'controller' => 'Ajax_UserStore', 'action' => 'getBranchesUser')) . '\'');
        $this->view->getJsManager()->addJsVar('urlGetPromotion','\'' . $this->view->url(array('module' => 'User', 'controller' => 'Promotion', 'action' => 'getPromotion')) . '\'');
        $this->view->getJsManager()->addJsVar('urlCalculatePromotionDelivery','\'' . $this->view->url(array('module' => '', 'controller' => 'Ajax_UserShipping', 'action' => 'calculatePromotionDelivery')) . '\'');
        $this->view->getJsManager()->addJsVar('urlSavePromotionSchedule','\'' . $this->view->url(array('module' => '', 'controller' => 'Ajax_UserShipping', 'action' => 'savePromotionSchedule')) . '\'');
        $this->view->getJsManager()->addJsVar('urlGetCredit','\'' . $this->view->url(array('module' => 'OperationController', 'controller' => 'Pagos', 'action' => 'getCreditos')) . '\'');
    }

    public function userAction() {
        $companyId = $this->getRequest()->getParam('id');
        $sessionUserId = Model3_Auth::getCredentials('id');


        $em = $this->getEntityManager('DefaultDb');

        $favoriteUserRepos = $em->getRepository('DefaultDb_Entities_FavoriteUsers');
        $favoriteFlagBuyer = false;
        $favoriteFlagSeller = false;

        $client = $em->find('DefaultDb_Entities_User', $sessionUserId);
        $favoriteClient = $em->find('DefaultDb_Entities_User', $companyId);
        $favoriteUsersBuyers = $favoriteUserRepos->findBy(array('client' => $client, 'favoriteClient' => $favoriteClient, 'type' => DefaultDb_Entities_FavoriteUsers::FAVORITE_BUYER));
        $favoriteUsersSellers = $favoriteUserRepos->findBy(array('client' => $client, 'favoriteClient' => $favoriteClient, 'type' => DefaultDb_Entities_FavoriteUsers::FAVORITE_SELLER));
        if (is_array($favoriteUsersBuyers) && count($favoriteUsersBuyers)) {
            $favoriteFlagBuyer = true;
        }
        if (is_array($favoriteUsersSellers) && count($favoriteUsersSellers)) {
            $favoriteFlagSeller = true;
        }

        $this->view->favoriteFlagBuyer = $favoriteFlagBuyer;
        $this->view->favoriteFlagSeller = $favoriteFlagSeller;
        $this->view->user = $em->find('DefaultDb_Entities_User', $companyId);
        $this->view->category = $this->view->user->getCategory();
        $this->view->getJsManager()->addJs('view/scripts/User/Store/user.js');
        $this->view->getCssManager()->addCss('view/scripts/User/Store/user.css');
        $this->view->getJsManager()->addJsVar('urlAddFavorite', '\'' . $this->view->url(array('module' => '', 'controller' => 'Ajax_Favorite', 'action' => 'addFavorite')) . '\'');
        $this->view->getJsManager()->addJsVar('urlRemoveFavorite', '\'' . $this->view->url(array('module' => '', 'controller' => 'Ajax_Favorite', 'action' => 'removeFavorite')) . '\'');
        $this->view->getJsManager()->addJsVar('commercialName', '\'' . $this->view->user->getCommercialName() . '\'');
    }

    /**
     * Obtiene el objeto de tipoDebito
     * depende del tipo de pago que son: Pago contra entrega, Pago al Momento 
     * y pago fuera de más distribución.
     * En pago contra entrega existen dos formas de debitar: congelar creditos y creditos negativos 
     * Son datos de la tabla tipo de debito
     */
    public function fncObtenerTipoDebito($formaDebito) {

        $CONGELAR_CREDITOS = 1;
        $NEGAR_CREDITOS = 2;
        $PAGO_MOMENTO = 3;
        $PAGO_FUERA = 4;

        $em = $this->getEntityManager('DefaultDb');
        if ($formaDebito == 0) {
            $tipo = $PAGO_MOMENTO;
        } else {
            $tipo = ($formaDebito == 1) ? $CONGELAR_CREDITOS : $NEGAR_CREDITOS;
        }
        if ($formaDebito == 4) {
            $tipo = $PAGO_FUERA;
        }

        $tipoDebito = $em->getRepository('DefaultDb_Entities_TipoDebito')->find($tipo);

        return $tipoDebito;
    }
    
    function fncValidar($txtCreditos, $txtTotalCreditos) {
        $result = false;
        $numCreditos = $txtCreditos;
        $numTotalCreditos = $txtTotalCreditos;
        $saldo = ($numCreditos - $numTotalCreditos);

        if ($numTotalCreditos > $numCreditos) {
            $result = true;
        }
        return $result;
    }
    
    public function fiscalDatAction() {/////FUNCION PARA OBTENER LOS DATOS DE LOS PUNTOS EN EL LISTADO DE TIENDAS////////////////////
    	
    	$companyId = $this->getRequest()->getPost();
    	 
    	$em = $this->getEntityManager('DefaultDb');

    	$resp = 'INFOI
    			<div style="border: solid 2px #32DC32;padding:10px;background-color:#f0ffe9; color: #000;">';

    	switch ($companyId['op']){

    		case '1'://///SI EL PUNTO DE VENTA TIENE USUARIO VINCULADO
    			
    			$company = $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $companyId['id']));
    			
    			$addrees = $company->getStreet();
    			$addrees .=", ".$company->getNumber();
    			$addrees .= ($company->getNumint() != "")?'-'.$company->getNumint():"";
    			$addrees .= ($company->getSuburb() != "")?", ".$company->getSuburb():"";
    			$addrees .= ($company->getZip() != "")?", C.P.".$company->getZip():"";
    			$addrees .= ($company->getState() != "")?", ".$company->getState()->getName():"";
    			$addrees .= ($company->getCity() != "")?", ".$company->getCity()->getName():"";
    			$addrees .= ($company->getCountry() != "")?", ".$company->getCountry()->getNombre():"";
    			
    			$resp .= '<p><strong>RFC: </strong>'.$company->getRfc().'</p>
	    			<p><strong>Direcci&oacute;n fiscal: </strong>'.$addrees.'</p>
	    			<p><strong>Tel&eacute;fono: </strong>'.$company->getPhone().', <strong>Correo: </strong>'.$company->getMail().'</p>
	    			<p><strong>Banco: </strong>'.$company->getBank().', <strong>Cuenta: </strong>'.$company->getAccount().', <strong>Clabe: </strong>'.$company->getClabe().'</p>
	    			<p><strong>PUNTOS REGISTRADOS </strong><br />';
    			 
    			foreach ($company->getBranches() as $branch){
    				if($branch->getPoint()->getDeleted() != 0){
    					 
    					if(strpos($branch->getPoint()->getAddress()->getAddress(),"-") != FALSE){
    						$lpos = strpos($branch->getPoint()->getAddress()->getAddress(),"-");
    					}else{
    						$lpos =strlen($branch->getPoint()->getAddress()->getAddress());
    					}
    					 
    					$resp .= '&#187 ';
    					$resp .= $branch->getPoint()->getName().', '.substr($branch->getPoint()->getAddress()->getAddress(), 0, $lpos).' '.$branch->getPoint()->getExtNumber();
    					$resp .= ($branch->getPoint()->getIntNumber() != "")?", Int. ".$branch->getPoint()->getIntNumber():"";
    					$resp .= ($branch->getPoint()->getNeighborhood() != "")?", Col. ".$branch->getPoint()->getNeighborhood():"";
    					$resp .= ($branch->getPoint()->getCity() != "")?", ".$branch->getPoint()->getAddress()->getCity()->getName():"";
    					$resp .= ($branch->getPoint()->getUrlGoogleMaps() != "")?' <a href="'.$branch->getPoint()->getUrlGoogleMaps().'" target="_blank"><span class="pers-btn icono-posicion2 tam-normal"></span></a><br />':"";
    					$resp .= '';
    				}
    			}
    			 
    			break;
   			
   			case '2'://///SI EL PUNTO DE VENTA NO TIENE USUARIO VINCULADO
   				
   				$company = $em->getRepository('DefaultDb_Entities_Point')->findOneBy(array('id' => $companyId['id']));
   				
   				if(strpos($company->getAddress()->getAddress(),"-") != FALSE){
   					$lpos = strpos($company->getAddress()->getAddress(),"-");
   				}else{
   					$lpos =strlen($company->getAddress()->getAddress());
   				}
   					
   				$resp .= '&#187 ';
   				$resp .= $company->getName().', '.substr($company->getAddress()->getAddress(), 0, $lpos).' '.$company->getExtNumber();
   				$resp .= ($company->getIntNumber() != "")?", Int. ".$company->getIntNumber():"";
   				$resp .= ($company->getNeighborhood() != "")?", Col. ".$company->getNeighborhood():"";
   				$resp .= ($company->getCity() != "")?", ".$company->getAddress()->getCity()->getName():"";
   				$resp .= ($company->getUrlGoogleMaps() != "")?' <a href="'.$company->getUrlGoogleMaps().'" target="_blank"><span class="pers-btn icono-posicion2 tam-normal"></span></a>':"";
   				$resp .= '';
   					
   				break;
    				 
    	}
    	
		$resp .= '</div>
				<div style="border: solid 1px #001f4d;padding:10px;background-color:#e6f0ff; color: #001f4d; font-size: .7em; text-align:center;  border-radius: 0px 0px 10px 10px;">
				<span class="pers-btn icono-info2 tam-normal"></span><br>DATOS PUBLICOS RECABADOS DE INTERNET
				</div>
				INFOF ';
    	      	
    	echo $resp;
    }
    
    public function viewAllProductsAction() {
    	$em = $this->getEntityManager('DefaultDb');
    	$this->view->getJsManager()->addJsVar('urlAddToCart', '\'' . $this->view->url(array('module' => 'User', 'controller' => 'AjaxCart', 'action' => 'addToCart')) . '\'');
    	$this->view->getJsManager()->addJsVar('urlcatalogall', '\'' . $this->view->url(array('module' => 'User', 'controller' => 'Store', 'action' => 'allproductGallery')) . '\'');
    	 
    	$seller = $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $this->getRequest()->getParam('id')));
    	$catalogs = $em->getRepository('DefaultDb_Entities_Catalog')->findBy(array('client' => $seller));
    	$this->view->catalogs = $catalogs;
    	$this->view->user = $seller;
    }
    
    public function allproductGalleryAction(){
    	$this->view->setUseTemplate(false);
    	 
    	$post = $this->getRequest()->getPost();
    	$catid = $post['dat'];
    	 
    	$predoduct = (isset($post['arrayp'])) ? $post['arrayp'] : "";
    
    	$em = $this->getEntityManager('DefaultDb');
    	$catalog = $em->getRepository('DefaultDb_Entities_Catalog')->findOneBy(array('id' => $catid ));
    
    	$result ="";
		$x = "Descripcion";
    	foreach ($catalog->getProducts() as $product){
			
			if($product->getStatus() === 1){
		
    		$div = '<span class="contimgdata"';
    		$contimgs = 0;
    		foreach ($product->getImages() as $idat){
    			if($contimgs == 0){//solo traer la primer imagen de la consulta que es la ultima en ser guardada
    				$div .= ' title="'.$product->getDescription().'" >';
					$div .= '<img loading="lazy" class="imgprod" id="img'.$product->getId().'" src="'.$this->view->getBaseUrl('/'.$idat->getPath()).'">';
    			}
    			$contimgs++;
    		}
			
			if($contimgs == 0){
					$div .=  " >";
			}
	
    		$checked = "";
    		if(is_array($predoduct)){
    			foreach ($predoduct as $precheck){
    				if(intval($precheck) == intval($product->getId())){
    					$checked = "checked";
    					break;
    				}
    			}
    		}
    		 
    		$div .= '<div style="width:80%; box-shadow:none;margin-top:1em;">
	   			<input type="checkbox" class="boxcheck" '.$checked.' id="bck'.$product->getId().'" name="productlist[]" title="'.$product->getName().'" value="'.$product->getId().'">
	   			<span >'.$product->getName().'</span><br />
	   			<span >Precio: $'.$product->getPrice().'</span><br />
	   			<span style="color:green;">Cr&eacute;ditos: $'.$product->getPriceCreditos().'</span>
	   			</div>';
    
    		$div .= '</span>';
    
    		$result .= $div;
			}
    	}
    	echo $result."--";
    }
    
}
