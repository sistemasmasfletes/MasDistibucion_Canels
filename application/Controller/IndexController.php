<?php

class IndexController extends Model3_Controller
{

    public function init()
    {
    	$dbs = Model3_Registry::getInstance()->get('databases');
    	$em = $dbs['DefaultDb'];
    	$productimage = $em->getRepository('DefaultDb_Entities_Product')->findBy(array('client' => 88));
    	
    	$this->view->imgp = $productimage;
    }

    public function indexAction()
    {
        $this->view->message = '';

        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $auth = new Model3_Auth();

            if ($auth->authenticate($post['mail'], md5($post['pass'])))
            {
                
            	$_SESSION['isapp'] = (isset($post['isapp']))? true : false;
            	 
                $credentials = Model3_Auth::getCredentials();
                switch ($credentials['type'])
                {
                    case DefaultDb_Entities_User::USER_ADMIN :
                        $this->redirect('Admin/Dashboard');
                        break;
                    case DefaultDb_Entities_User::USER_CLIENT :
                        $_SESSION['firstlog'] = 1;
                    	$this->redirect('User/BranchesUser');
                        break;                   
                    default:                        
                        $this->redirect('App/');
                        break;
                }
            }
            else
            {
                $this->view->message = "Usuario o password incorrecto";
            }
        }
    }

    public function pantryAction(){
    	$dbs = Model3_Registry::getInstance()->get('databases');
    	$em = $dbs['DefaultDb'];
    	$productimage = $em->getRepository('DefaultDb_Entities_Product')->findBy(array('client' => 88, 'catalog' => 47));
    	$this->view->getJsManager()->addJsVar('urlCheckUser', '\'' . $this->view->url(array('module' => '', 'controller' => 'Index', 'action' => 'checkUser')) . '\'');
    	 
    	$this->view->products = $productimage;
    	$this->view->message = '';
    	
    	if ($this->getRequest()->isPost()){
    		$post = $this->getRequest()->getPost();
    		
    		$newuser = TRUE;
    		if(isset($post['submit1'])){
    			$user= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('username' => $post['username'], 'password' => md5($post['password'])));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
    			if(!is_object($user)){
    				$this->view->message = '<div style="background-color:red; border-radius:5px; font-size:1em;padding:0.5em;color:white;">&iexcl;ATENCI&Oacute;N! USUARIO Y/O CONTRASE&Ntilde;A INCORRECTOS</div>';
    				return false;
    			}
    			$newuser = FALSE;
    		}
    		
    		if(isset($post['submit2'])){
    			
    			$userconfirm= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('username' => $post['username']),md5($post['password']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
    			if(is_object($userconfirm)){
    				$this->view->message = '<div style="background-color:red; border-radius:5px; font-size:1em;padding:0.5em;color:white;">&iexcl;ATENCI&Oacute;N! EL CORREO YA ESTA REGISTRADO EN MASDISTRIBUCION</div>';
    				return false;
    			}
    			 
    			if ($post['pass'] == $post['passconfirmation']){
    				 
    				$parent= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => 32));
    				$role= $em->getRepository('DefaultDb_Entities_Role')->findOneBy(array('id' => 3));
    				$category= $em->getRepository('DefaultDb_Entities_Category')->findOneBy(array('id' => 173));
    				$moneda= $em->getRepository('DefaultDb_Entities_TipoMonedas')->findOneBy(array('id' => 2));
    			
    				$user = new DefaultDb_Entities_User();//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD Contacts
    				$branchuser = new DefaultDb_Entities_BranchesUser();
					
					$em->getConnection()->beginTransaction();//SE COMIENZA CON EL PROCESO DE GUARDAR LOS NUEVOS DATOS
    				try{
    					 
    					$user->setUsername($post['email'], ENT_QUOTES, "UTF-8");
    					$user->setRegistration(date("Y-m-d H:i:s"));
						$user->setPassword(md5($post['pass']));
    					$user->setFirstName($post['firstName'], ENT_QUOTES, "UTF-8");
    					$user->setLastName($post['lastName'], ENT_QUOTES, "UTF-8");
    					$user->setCellPhone($post['cellPhone']);
    					$user->setCommercialName($post['firstName']." ".$post['lastName'], ENT_QUOTES, "UTF-8");
    					$user->setDayInvoice(0);
    					$user->setCode("Establecer");
    					$user->setPoint(NULL);
    					$user->setTerms(1);
    					$user->setService(1);
    					$user->setPrivacy(1);
    					$user->setVisible(0);
    					$user->setCrdPay(0);
    					$user->setDelyPay(0);
    					$user->setType(3);
    					$user->setRole($role);
    					$user->setParent($parent);
    					$user->setCategory($category);
    					$user->setMoneda($moneda);
    					$user->setMail($post['email'], ENT_QUOTES, "UTF-8");
    			
    					$em->persist($user);
    					$em->flush();
    			
    					$branchuser->setClient($user);
    					$branchuser->setName($post['firstName']." ".$post['lastName']);
    					$branchuser->setDirection($post['street'].", ".$post['number'].", ".$post['inside'].", ".$post['suburb'].", ".$post['cp']);
    					$branchuser->setUrlmaps($post['urlmaps']);
    			
    					$em->persist($branchuser);
    					$em->flush();
    			
    					$provdefault = [222,203,198,163,88];
    			
    					foreach ($provdefault as $id) {
    						$favoriteUser = new DefaultDb_Entities_FavoriteUsers();
    						$fdefault= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $id));
    						 
    						$favoriteUser->setClient($user);
    						$favoriteUser->setFavoriteClient($fdefault);
    						$favoriteUser->setType(2);
    						$favoriteUser->setproducto(null);
    						 
    						$em->persist($favoriteUser);
    						$em->flush();
    					}
    					$em->getConnection()->commit();
    			
    					$this->view->message = '<div style="background-color:#d9edf7; color:#3a87ad; width:90%; border-radius:5px; padding:1em;text-align:center;">Se ha registrado con exito!</div><br />';
    					$this->view->success = "1";
    			
    				}catch(Exception $e){
    					$em->getConnection()->rollback();
    					$em->close();
    					$this->view->message = $e->getMessage();
    				}
    			}else{
    				$this->view->message = '<div style="background-color:#FFB8B8; color:#FF0000; width:90%; border-radius:5px; padding:1em;text-align:center;">Las contrase&ntilde;as no coinciden!</div>';
    			}    			
    		}
    		
    		$seller= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => 88));
    		
    		$order = new DefaultDb_Entities_M3CommerceOrder();
    		$order->setBuyer($user);
    		$order->setSeller($seller);
    		$order->setCreationDate(new DateTime());
   			$order->setComments('');
   			
   			foreach ($_POST['productlist'] as $products=>$idproduct) {
    			$product = $em->find('DefaultDb_Entities_Product', $idproduct);
    			if ($product && $product->getStock() >= 1) {
    				$productToOrder = new DefaultDb_Entities_M3CommerceProductToOrder();
    				$productToOrder->setProduct($product);
    				$product->setStock($product->getStock() - 1);
    				$productToOrder->setQuantity(1);
    				$productToOrder->setPrice($product->getPrice() * 1);
    				$order->addProduct($productToOrder);
    			} else {
    				$this->view->error_stock = true;
    				return;
    			}
   			}
    		$em->persist($order);
    		$em->flush();

    		$tipoconcepto= $em->getRepository('DefaultDb_Entities_TipoConcepto')->findOneBy(array('id' => 4));
    		$tipoconcepto1= $em->getRepository('DefaultDb_Entities_TipoConcepto')->findOneBy(array('id' => 3));
    		$tipodebito= $em->getRepository('DefaultDb_Entities_TipoDebito')->findOneBy(array('id' => 4));
    		
    		$pago = new DefaultDb_Entities_Pagos();
    		$pago->setOrden($order->getId());
    		$pago->setMontoCompra(0);
    		$pago->setMontoCreditos(0);
    		$pago->setFecha(new DateTime());
    		$pago->setEstatus(2);
    		$pago->setTimestamp(new DateTime());
    		$pago->setDescripcion("Compra a ".$seller->getFirstName()." ".$seller->getLastName());
    		$pago->setUsuario($user);
    		$pago->setCliente($seller);
    		$pago->setCompraVenta($order);
    		$pago->setTipoConcepto($tipoconcepto);
    		$pago->setTipoDebito($tipodebito);
    		
    		$em->persist($pago);
    		$em->flush();
    		
    		$pago1 = new DefaultDb_Entities_Pagos();
    		$pago1->setOrden($order->getId());
    		$pago1->setMontoCompra(0);
    		$pago1->setMontoCreditos(0);
    		$pago1->setFecha(new DateTime());
    		$pago1->setEstatus(2);
    		$pago1->setTimestamp(new DateTime());
    		$pago1->setDescripcion("Venta a ".$user->getFirstName()." ".$user->getLastName());
    		$pago1->setUsuario($seller);
    		$pago1->setCliente($user);
    		$pago1->setCompraVenta($order);
    		$pago1->setTipoConcepto($tipoconcepto1);
    		$pago1->setTipoDebito($tipodebito);
    		
    		$em->persist($pago1);
    		$em->flush();    	
    			
    		if($newuser){
    			
    			$correo = "<html><body>
	            			<b>Hola controlador revisa las sucursales por aprobar,<br />
			        		se ha generado un nuevo registro de usuario con los siguientes datos:</b><br />
			        		<b>Nombre:</b> ".$post['firstName']." ".$post['lastName']."<br />
			        		<b>Comercio:</b> ".$post['firstName']." ".$post['lastName']/*$post['commercialname']*/."<br />
			        		<b>Correo:</b> ".$post['email']."<br />
			        		<b>Tel&eacute;fono:</b> ".$post['cellPhone']."<br />
			        		<b>Direcci&oacute;n:</b> ".$post['street'].", ".$post['number'].", ".$post['inside'].", ".$post['suburb'].", ".$post['cp']."<br />
			        		<b>Ubicaci&oacute;n:</b> <a href='".$post['urlmaps']."'>Maps</a><br />
			        		</span><br />
	            			</body></html>";
    			$mailsend = $this->sendMail($correo,$post['email'],"masdistribucion.ventas@gmail.com"/*"emmanuelroket@gmail.com"*/,"Aviso de registro");    			
    		
    			$correo2 = "<html><body>
	            			<b>Gracias por registrarte en masdistribucion.com nos pondremos en contacto contigo<br />
			        		para asignar los dias de entrega en tu domicilio</b><br />
	            			</body></html>";
    			$mailsend2 = $this->sendMail($correo2,"masdistribucion.ventas@gmail.com",$post['email'],"Confirmacion de registro");

    			$controleruser= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => 32));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
    			    
    			$data = array(
    					'title' => 'Nuevo registro',
    					'body' => 'Se ha registrado un nuevo usuario en masdistribucion.com.',
    			);
    			
    			$this->sendPushNotification($controleruser->getToken(),  $data);
    		}
    		
    		$correo1 = "<html><body>
	            		<span>REVISA TUS EMBARQUES EN masdistribucion.com, EL CLIENTE:".$user->getFirstName()." ".$user->getLastName()." </span><br />
	            		<span>GENERO LA ORDEN:".$order->getId()." PARA UNA DESPENSA CONTACTALO AL CORREO: ".$user->getMail()."</span>
	            		</body></html>";
   			$mailsend1 = $this->sendMail($correo1,$user->getMail(),$seller->getMail()/*"emmanuelroket@gmail.com"*/,"Aviso de pedido de despensa");
    		
    		$this->view->message = '<div style="background-color:green; border-radius:5px; font-size:1em;padding:0.5em;color:white;">&iexcl;FELICIDADES! Tu despensa esta en proceso de envio, nos pondremos en contacto contigo y/o puedes ingresar <a href="'.$this->view->url(array('controller' => 'Index','action' => 'index')).'" >AQU&Iacute;</a> con tu correo y password</div>';
    	}	
    }
    
    public function searchProductAction(){
    	$dbs = Model3_Registry::getInstance()->get('databases');
    	$em = $dbs['DefaultDb'];
    	$this->view->getJsManager()->addJsVar('urlCheckUser', '\'' . $this->view->url(array('module' => '', 'controller' => 'Index', 'action' => 'checkUser')) . '\'');
    
		$this->view->storename = "";    
    	$this->view->message = '';
    	$this->view->serchmsg = "";
    	$arrproducts = array();
    	$firstproduct = array();
    	$this->view->view = 1;
    	$this->view->sellerid = "";
		 
		if (Model3_Auth::isAuth()){
			$this->view->getJsManager()->addJsVar('urlAddToCart', '\'' . $this->view->url(array('module' => 'User', 'controller' => 'AjaxCart', 'action' => 'addToCart')) . '\'');
		}

    	if ($this->getRequest()->isPost()){
    		$post = $this->getRequest()->getPost();
    		$pruducts = $em->getRepository('DefaultDb_Entities_Product');
    		
    		if(isset($post['submit3'])){//SI SE CUMPLE ES LA BUSQUEDA
    			$this->view->serchmsg = "<span>Resultado: <span>";
    			$this->view->view = 1;
    			 
    			$arrproducts = $pruducts->createQueryBuilder('l')
				->where("l.name like '%".$post['searchproduct']."%'")
    			->orderBy("l.name","DESC")
    			//->groupBy("l.client")
    			->getQuery()
    			->getResult();
    			
    			//$this->view->serchmsg .= (count($arrproducts) > 0) ? ' Los siguientes proveedores tienen tu producto "'.$post['searchproduct'].'"' : ' No hay coincidencias para tu busqueda "'.$post['searchproduct'].'"' ;
    			$this->view->serchmsg .= (count($arrproducts) > 0) ? '"'.$post['searchproduct'].'"' : ' No hay coincidencias para tu busqueda "'.$post['searchproduct'].'"' ;
			}
    		
    		if(isset($post['clientid'])){//SI SE CUMPLE ES POR QUE SELECCIONO UN PROVEEDOR Y SE MOSTRARAN SUS PRODUCTOS
    			
    			$productdata = explode("-",$post['clientid']);
	    		$store= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $post['clientid']));				
    			
    			$firstproduct = $pruducts->createQueryBuilder('l')
    			->where("l.id =".$productdata[1])
    			->getQuery()
    			->getResult();
    			
    			$arrproducts = $pruducts->createQueryBuilder('l')
    			->where("l.client =".$productdata[0])
    			->andWhere("l.id <> ".$productdata[1])
    			->orderBy("l.id","DESC")
    			->getQuery()
    			->getResult();
    			$this->view->view = 2;
				$this->view->sellerid = $post['clientid'];
				$this->view->storename = '<div style="padding:0.5em; text-align:center; font-size:2em; line-height:1em;">Cat&aacute;logo de la tienda:<br >'.$store->getCommercialName().'</div>';
				$this->view->storename .= '<div style="padding:0.5em;"><input type="text" name="intersearch" id="intersearch" value="" placeholder="Buscar productos en este cat&aacute;logo" ></div>';
    		}

    		if(isset($post['submit2'] ) || isset($post['submit1'] )){//SI SE CUMPLE QUIERE DECIR QUE SE ESTA HACIENDO LA COMPRA DE PRODUCTOS

    			$newuser = TRUE;//si se conserva este valor se trata de un nuevo usuario y se deben generar los registros necesarios
	    		if(isset($post['submit1'])){
	    			$user= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('username' => $post['username'], 'password' => md5($post['password'])));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
	    			
	    			if(!is_object($user)){
	    				$this->view->message = '<div style="background-color:red; border-radius:5px; font-size:1em;padding:0.5em;color:white;">&iexcl;ATENCI&Oacute;N! USUARIO Y/O CONTRASE&Ntilde;A INCORRECTOS</div>';
	    				$this->view->products = $arrproducts;
    					$this->view->firstproduct = $firstproduct;
    	   				return false;
	    			}
	    			$newuser = FALSE;
	    		}

	    		if(isset($post['submit2'])){
	    			 
	    			$userconfirm= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('username' => $post['username']),md5($post['password']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
	    			if(is_object($userconfirm)){
	    				$this->view->message = '<div style="background-color:red; border-radius:5px; font-size:1em;padding:0.5em;color:white;">&iexcl;ATENCI&Oacute;N! EL CORREO YA ESTA REGISTRADO EN MASDISTRIBUCION</div>';
	    				$this->view->products = $arrproducts;
    					$this->view->firstproduct = $firstproduct;
    	   				return false;
	    			}
	    
	    			if ($post['pass'] == $post['passconfirmation']){
	    					
	    				$parent= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => 32));
	    				$role= $em->getRepository('DefaultDb_Entities_Role')->findOneBy(array('id' => 3));
	    				$category= $em->getRepository('DefaultDb_Entities_Category')->findOneBy(array('id' => 173));
	    				$moneda= $em->getRepository('DefaultDb_Entities_TipoMonedas')->findOneBy(array('id' => 2));
	    				 
	    				$user = new DefaultDb_Entities_User();//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD Contacts
	    				$branchuser = new DefaultDb_Entities_BranchesUser();
	    				$em->getConnection()->beginTransaction();//SE COMIENZA CON EL PROCESO DE GUARDAR LOS NUEVOS DATOS
	    				try{
	    
	    					$user->setUsername($post['email'], ENT_QUOTES, "UTF-8");
							$user->setRegistration(date("Y-m-d H:i:s"));
	    					$user->setPassword(md5($post['pass']));
	    					$user->setFirstName($post['firstName'], ENT_QUOTES, "UTF-8");
	    					$user->setLastName($post['lastName'], ENT_QUOTES, "UTF-8");
	    					$user->setCellPhone($post['cellPhone']);
	    					$user->setCommercialName($post['firstName']." ".$post['lastName'], ENT_QUOTES, "UTF-8");
	    					$user->setDayInvoice(0);
	    					$user->setCode("Establecer");
	    					$user->setPoint(NULL);
	    					$user->setTerms(1);
	    					$user->setService(1);
	    					$user->setPrivacy(1);
	    					$user->setVisible(0);
	    					$user->setCrdPay(0);
	    					$user->setDelyPay(0);
	    					$user->setType(3);
	    					$user->setRole($role);
	    					$user->setParent($parent);
	    					$user->setCategory($category);
	    					$user->setMoneda($moneda);
	    					$user->setMail($post['email'], ENT_QUOTES, "UTF-8");
	    					 
	    					$em->persist($user);
	    					$em->flush();
	    					 
	    					$branchuser->setClient($user);
	    					$branchuser->setName($post['firstName']." ".$post['lastName']);
	    					$branchuser->setDirection($post['street'].", ".$post['number'].", ".$post['inside'].", ".$post['suburb'].", ".$post['cp']);
	    					$branchuser->setUrlmaps($post['urlmaps']);
	    					 
	    					$em->persist($branchuser);
	    					$em->flush();
	    					 
	    					$provdefault = [222,203,198,163,88];
	    					 
	    					foreach ($provdefault as $id) {
	    						$favoriteUser = new DefaultDb_Entities_FavoriteUsers();
	    						$fdefault= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $id));
	    							
	    						$favoriteUser->setClient($user);
	    						$favoriteUser->setFavoriteClient($fdefault);
	    						$favoriteUser->setType(2);
	    						$favoriteUser->setproducto(null);
	    							
	    						$em->persist($favoriteUser);
	    						$em->flush();
	    					}
	    					$em->getConnection()->commit();
	    					 
	    					$this->view->message = '<div style="background-color:#d9edf7; color:#3a87ad; width:90%; border-radius:5px; padding:1em;text-align:center;">Se ha registrado con exito!</div><br />';
	    					$this->view->success = "1";
	    					 
	    				}catch(Exception $e){
	    					$em->getConnection()->rollback();
	    					$em->close();
	    					$this->view->message = $e->getMessage();
	    				}
	    			}else{//si las contraseñas del nuevo registro no coinciden
	    				$this->view->message = '<div style="background-color:#FFB8B8; color:#FF0000; width:90%; border-radius:5px; padding:1em;text-align:center;">Las contrase&ntilde;as no coinciden!</div>';
	    				$this->view->products = $arrproducts;
				    	$this->view->firstproduct = $firstproduct;
	    				return false;
	    			}
	    		}
	    
	    		$seller= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $post['sellerid']));
	    
	    		$order = new DefaultDb_Entities_M3CommerceOrder();
	    		$order->setBuyer($user);
	    		$order->setSeller($seller);
	    		$order->setCreationDate(new DateTime());
	    		$order->setComments('');
	    
	    		foreach ($_POST['productlist'] as $products=>$idproduct) {
	    			$product = $em->find('DefaultDb_Entities_Product', $idproduct);
	    			if ($product && $product->getStock() >= 1) {
	    				$productToOrder = new DefaultDb_Entities_M3CommerceProductToOrder();
	    				$productToOrder->setProduct($product);
	    				$product->setStock($product->getStock() - 1);
	    				$productToOrder->setQuantity(1);
	    				$productToOrder->setPrice($product->getPrice() * 1);
	    				$order->addProduct($productToOrder);
	    			} else {
	    				$this->view->error_stock = true;
	    				return;
	    			}
	    		}
	    		$em->persist($order);
	    		$em->flush();
	    
	    		$tipoconcepto= $em->getRepository('DefaultDb_Entities_TipoConcepto')->findOneBy(array('id' => 4));
	    		$tipoconcepto1= $em->getRepository('DefaultDb_Entities_TipoConcepto')->findOneBy(array('id' => 3));
	    		$tipodebito= $em->getRepository('DefaultDb_Entities_TipoDebito')->findOneBy(array('id' => 4));
	    
	    		$pago = new DefaultDb_Entities_Pagos();
	    		$pago->setOrden($order->getId());
	    		$pago->setMontoCompra(0);
	    		$pago->setMontoCreditos(0);
	    		$pago->setFecha(new DateTime());
	    		$pago->setEstatus(2);
	    		$pago->setTimestamp(new DateTime());
	    		$pago->setDescripcion("Compra a ".$seller->getFirstName()." ".$seller->getLastName());
	    		$pago->setUsuario($user);
	    		$pago->setCliente($seller);
	    		$pago->setCompraVenta($order);
	    		$pago->setTipoConcepto($tipoconcepto);
	    		$pago->setTipoDebito($tipodebito);
	    
	    		$em->persist($pago);
	    		$em->flush();
	    
	    		$pago1 = new DefaultDb_Entities_Pagos();
	    		$pago1->setOrden($order->getId());
	    		$pago1->setMontoCompra(0);
	    		$pago1->setMontoCreditos(0);
	    		$pago1->setFecha(new DateTime());
	    		$pago1->setEstatus(2);
	    		$pago1->setTimestamp(new DateTime());
	    		$pago1->setDescripcion("Venta a ".$user->getFirstName()." ".$user->getLastName());
	    		$pago1->setUsuario($seller);
	    		$pago1->setCliente($user);
	    		$pago1->setCompraVenta($order);
	    		$pago1->setTipoConcepto($tipoconcepto1);
	    		$pago1->setTipoDebito($tipodebito);
	    
	    		$em->persist($pago1);
	    		$em->flush();
    		 
	    		if($newuser){//si es un usuario nuevo se envian los correos correspondientes de nuevo usuario
	    			 
	    			$correo = "<html><body>
		            			<b>Hola controlador revisa las sucursales por aprobar,<br />
				        		se ha generado un nuevo registro de usuario con los siguientes datos:</b><br />
				        		<b>Nombre:</b> ".$post['firstName']." ".$post['lastName']."<br />
				        		<b>Comercio:</b> ".$post['firstName']." ".$post['lastName']."<br />
				        		<b>Correo:</b> ".$post['email']."<br />
				        		<b>Tel&eacute;fono:</b> ".$post['cellPhone']."<br />
				        		<b>Direcci&oacute;n:</b> ".$post['street'].", ".$post['number'].", ".$post['inside'].", ".$post['suburb'].", ".$post['cp']."<br />
				        		<b>Ubicaci&oacute;n:</b> <a href='".$post['urlmaps']."'>Maps</a><br />
				        		</span><br />
		            			</body></html>";
	    			$mailsend = $this->sendMail($correo,$post['email'],"masdistribucion.ventas@gmail.com","Aviso de registro");
	    
	    			$correo2 = "<html><body>
		            			<b>Gracias por registrarte en masdistribucion.com nos pondremos en contacto contigo<br />
				        		para asignar los dias de entrega en tu domicilio</b><br />
		            			</body></html>";
	    			
	    			$mailsend2 = $this->sendMail($correo2,"masdistribucion.ventas@gmail.com",$post['email'],"Confirmacion de registro");
	    
	    			$controleruser= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => 32));
	    				
	    			$data = array(
	    					'title' => 'Nuevo registro',
	    					'body' => 'Se ha registrado un nuevo usuario en masdistribucion.com.',
	    			);
	    			 
	    			$this->sendPushNotification($controleruser->getToken(),  $data);
	    		}
	    
	    		$correo1 = "<html><body>
		            		<span>REVISA TUS EMBARQUES EN masdistribucion.com, EL CLIENTE:".$user->getFirstName()." ".$user->getLastName()." </span><br />
		            		<span>GENERO LA ORDEN:".$order->getId()." COMPRA DE PRODUCTOS, CONTACTALO AL CORREO: ".$user->getMail()."</span>
		            		</body></html>";
	    		$mailsend1 = $this->sendMail($correo1,$user->getMail(),$seller->getMail(),"Aviso de pedido de producto");
	    
	    		$this->view->message = '<div style="background-color:green; border-radius:5px; font-size:1em;padding:0.5em;color:white;">&iexcl;FELICIDADES! Tu compra esta en proceso de envio, nos pondremos en contacto contigo y/o puedes ingresar <a href="'.$this->view->url(array('controller' => 'Index','action' => 'index')).'" >AQU&Iacute;</a> con tu correo y password</div>';
    		}
    	}//TERMINA IF DEL POST    	
    	$this->view->products = $arrproducts;
    	$this->view->firstproduct = $firstproduct;
    }    
    
    public function checkUserAction(){
        $this->view->setUseTemplate(false);
    	$post = $this->getRequest()->getPost();
    	 
    	$email = trim($post['dat']); 
    	
    	$dbs = Model3_Registry::getInstance()->get('databases');
    	$em = $dbs['DefaultDb'];

	    $preuser= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('username' => $email));
	    $result = (is_object($preuser))?"TRUE":"FALSE";
    	echo $result."--";
    }
    
    public function registerAction(){

    	$this->view->success = "0";
    	
    	if (!Model3_Auth::isAuth()){
	    	
	        $this->view->getJsManager()->addJs('jquery/jquery.min.v1.4.2.js');
	        $this->view->getJsManager()->addJs('jquery/jquery.validate.js');
	        $this->view->getJsManager()->addJs('tiny_mce/tiny_mce.js');
	        $this->view->getJsManager()->addJs('tiny_mce/tiny_mce_src.js');
	        $this->view->getJsManager()->addJs('text.js');
	        $this->view->getJsManager()->addJs('register/register.js');
	        $this->view->getJsManager()->addJs('sheepIt/jquery.sheepItPlugin-1.0.0.min.js');
	        $this->view->getJsManager()->addJs('register/documents.js');
	        $this->view->getJsManager()->addJs('jquery/jquery-ui-1.8.12.custom.min.js');
	        $this->view->getJsManager()->addJs('register/autocomplete.js');
	        
	        $dbs = Model3_Registry::getInstance()->get('databases');
	        $em = $dbs['DefaultDb'];
	         
	        $categorylist= $em->getRepository('DefaultDb_Entities_Category')->findBy(array(),array('name' => 'ASC' ));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
			
	        $clist="";
	        foreach ($categorylist as $category){
	        		$clist .= '<option value="'.$category->getId().'">'.$category->getName().'</option>';
	        }
	        
	        $this->view->optionlist = $clist;
	        
	        if ($this->getRequest()->isPost()){
	
	        	$post = $this->getRequest()->getPost();

	        	$preuser= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('username' => $post['email']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
	        	$controleruser= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => 32));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
	        	
	        	if(!is_object($preuser)){
	        		
					/*if(!$this->is_valid_email($post['email'])){
						$this->view->message = '<div style="background-color:#FFB8B8; color:#FF0000; width:90%; border-radius:5px; padding:1em;text-align:center;">El formato de Correo no es valido!</div>';
						return false;
					}*/        		
	        		
		        	if ($post['pass'] == $post['passconfirmation']){
		        		 
			        	$parent= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => 32));
			        	$role= $em->getRepository('DefaultDb_Entities_Role')->findOneBy(array('id' => 3));
			        	$category= ($post['category'] != "")?$em->getRepository('DefaultDb_Entities_Category')->findOneBy(array('id' => $post['category'])):$em->getRepository('DefaultDb_Entities_Category')->findOneBy(array('id' => 173));
			        	$moneda= $em->getRepository('DefaultDb_Entities_TipoMonedas')->findOneBy(array('id' => 2));
			        	
			        	$item = new DefaultDb_Entities_User();//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD Contacts
						$branchuser = new DefaultDb_Entities_BranchesUser();
			        	$em->getConnection()->beginTransaction();//SE COMIENZA CON EL PROCESO DE GUARDAR LOS NUEVOS DATOS
			        	try{
			        		
			        		$item->setUsername($post['email'], ENT_QUOTES, "UTF-8");
							$item->setRegistration(date("Y-m-d H:i:s"));
			        		$item->setPassword(md5($post['pass']));
			        		$item->setFirstName($post['firstName'], ENT_QUOTES, "UTF-8");
			        		$item->setLastName($post['lastName'], ENT_QUOTES, "UTF-8");
			        		$item->setCellPhone($post['cellPhone']);
			        		$item->setCommercialName(($post['commercialname']!="")?$post['commercialname']:$post['firstName']." ".$post['lastName'], ENT_QUOTES, "UTF-8");
			        		$item->setDayInvoice(0);
			        		$item->setCode("Establecer");
			        		$item->setPoint(NULL);
			        		$item->setTerms(1);
			        		$item->setService(1);
			        		$item->setPrivacy(1);
			        		$item->setVisible(0);
			        		$item->setCrdPay(0);
			        		$item->setDelyPay(0);
			        		$item->setType(3);
			        		$item->setRole($role);
			        		$item->setParent($parent);
			        		$item->setCategory($category);
			        		$item->setMoneda($moneda);
			        		$item->setMail($post['email'], ENT_QUOTES, "UTF-8");
			        		 
			        		$em->persist($item);
			        		$em->flush();
			        		 
			        		$branchuser->setClient($item);
			        		$branchuser->setName(($post['commercialname']!="")?$post['commercialname']:$post['firstName']." ".$post['lastName']);
			        		$branchuser->setDirection($post['street'].", ".$post['number'].", ".$post['inside'].", ".$post['suburb'].", ".$post['cp']);
			        		$branchuser->setUrlmaps($post['urlmaps']);
			        		 
			        		$em->persist($branchuser);
			        		$em->flush();
			        	
			        		$provdefault = [222,203,198,163,88];
			        		 
				        	foreach ($provdefault as $id) {
				        		$favoriteUser = new DefaultDb_Entities_FavoriteUsers();
				        		$fdefault= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $id));
			        		
			        			$favoriteUser->setClient($item);
			        			$favoriteUser->setFavoriteClient($fdefault);
			        			$favoriteUser->setType(2);
			        			$favoriteUser->setproducto(null);
			        			 
			        			$em->persist($favoriteUser);
			        			$em->flush();
			        		}
			        		
			        		$em->getConnection()->commit();

			        		$this->view->message = '<div style="background-color:#d9edf7; color:#3a87ad; width:90%; border-radius:5px; padding:1em;text-align:center;">Se ha registrado con exito!</div><br />';
			        		$this->view->success = "1";
			        		//$this->redirect('Index/index');
			        		
			        		$correo = "<html><body>
	            			<b>Hola controlador revisa las sucursales por aprobar,<br />
			        		se ha generado un nuevo registro de usuario con los siguientes datos:</b><br />
			        		<b>Nombre:</b> ".$post['firstName']." ".$post['lastName']."<br />
			        		<b>Comercio:</b> ".$post['commercialname']."<br />
			        		<b>Correo:</b> ".$post['email']."<br />
			        		<b>Tel&eacute;fono:</b> ".$post['cellPhone']."<br />
			        		<b>Direcci&oacute;n:</b> ".$post['street'].", ".$post['number'].", ".$post['inside'].", ".$post['suburb'].", ".$post['cp']."<br />
			        		<b>Ubicaci&oacute;n:</b> <a href='".$post['urlmaps']."'>Maps</a><br />
			        		</span><br />
	            			</body></html>";
			        		 
			        		$mailsend = $this->sendMail($correo,$post['email'],"masdistribucion.ventas@gmail.com","Aviso de registro");			        		
			        		
			        		$correo1 = "<html><body>
	            			<b>Gracias por registrarte en masdistribucion.com nos pondremos en contacto contigo<br />
			        		para asignar los dias de entrega en tu domicilio</b><br />
	            			</body></html>";
			        		 
			        		$mailsend1 = $this->sendMail($correo1,"masdistribucion.ventas@gmail.com",$post['email'],"Confirmacion de registro");
			        		 
			        		$data = array(
			        				'title' => 'Nuevo registro',
			        				'body' => 'Se ha registrado un nuevo usuario en masdistribucion.com.',
			        		);
			        		 
			        		$this->sendPushNotification($controleruser->getToken(),  $data);
			        		
			        	}catch(Exception $e){
			        		$em->getConnection()->rollback();
			        		$em->close();
		        			$this->view->message = $e->getMessage();
			        	}
		        	}else{
	        			$this->view->message = '<div style="background-color:#FFB8B8; color:#FF0000; width:90%; border-radius:5px; padding:1em;text-align:center;">Las contrase&ntilde;as no coinciden!</div>';
		        	}      
	        	}else{
	        		$this->view->message = '<div style="background-color:#FFB8B8; color:#FF0000; width:90%; border-radius:5px; padding:1em;text-align:center;">El nombre de Usuario ya esta Registrado!</div>';
	        	}
	        } 	
    	}else{
    		$this->redirect('User/BranchesUser');
    	}
    }

    public function is_valid_email($str)
    {
    	return (false !== filter_var($str, FILTER_VALIDATE_EMAIL));
    }
    
    public function logoutAction()
    {
        Model3_Auth::deleteCredentials();
        unset($_SESSION['isapp']);
        $this->redirect('Index/index/');
    }

    public function recoverPassAction()
    {
        
    }

    public function sendPassAction()
    {
    	$this->view->res = false;
    	$this->view->msg = false;
    	 
        if ($this->getRequest()->isPost()){
            $post = $this->getRequest()->getPost();
            $dbs = Model3_Registry::getInstance()->get('databases');
            $em = $dbs['DefaultDb'];
            /* @var $userSearch DefaultDb_Entities_User */
            $userSearch = $em->getRepository('DefaultDb_Entities_User')->searchUserByEmail($post['email']);
            //var_dump($userSearch->getId());
            if ($userSearch){
                $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
                $newPass = "";
                for ($i = 0; $i < 8; $i++)
                    $newPass .= substr($str, rand(0, 62), 1);
                //echo $userSearch->getId();
                $userSearch->setPassword(md5($newPass));
                $em->persist($userSearch);
                $em->flush();
                $this->view->res = true;
                $this->view->msg = 'Revisa tu correo electrónico, se ha reestablecido tu contraseña, también podría aparecer en la bandeja de SPAM';
                
                $correo = "<html><body>
	            			<b>Se ha restablecido tu contraseña a una provisional:<br />
			        		".$newPass." usala para acceder a tu cuenta en masdistribucion.com, te recomendamos modificarla lo antes posible en la sección Mis datos -> Password </b><br />
	            			</body></html>";
                
                $mailsend = $this->sendMail($correo,"masdistribucion.ventas@gmail.com",$post['email'],"Restablecer contraseña");

                $correo2 = "<html><body>
	            			<b>El usuario: ".$post['email']."<br />
							solicito un cambio de contraseña, se genero provisionalmente ".$newPass."  </b><br />
	            			</body></html>";
                
                $mailsend2 = $this->sendMail($correo2,"masdistribucion.ventas@gmail.com","masdistribucion.ventas@gmail.com","Restablecer contraseña");
                
                //$helperMail = new Helper_MailHelper();
                //$helperMail->sendPassword($post['email'], $newPass);
            }else{
                $this->view->res = false;
                $this->view->msg = 'Este correo no esta registrado';
            }
        }
    }

    public function AxValidateEmailAction()
    {
        $this->view->setUseTemplate(false);
        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $dbs = Model3_Registry::getInstance()->get('databases');
            $em = $dbs['DefaultDb'];

            /* @var $userSearch DefaultDb_Entities_User */
            $user = $em->getRepository('DefaultDb_Entities_User')->searchUserByEmail($post['email']);
            if ($user && $user->getTypeLoginUser() == 3)
                $this->view->res = true;
        }
    }

    public function aboutDealAction()
    {
        
    }

    public function contactUsAction()
    {
        $this->view->getJsManager()->addJs('jquery/jquery.validate.js');
        $this->view->getJsManager()->addJs('contactUs/contactUs.js');

        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        $event = new DefaultDb_Entities_Contacts();
        $admin = array('hector.benitez.fletes@gmail.com', 'cortestavo@gmail.com');
//        $admin = array('hector.benitez.fletes@gmail.com');
		
        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $event->setFirstName($post['firstName']);
            $event->setLastName($post['lastName']);
            $event->setUsername($post['username']);
            $event->setPnone($post['telephone']);
            $event->setEventName($post['eventName']);
            $event->setEventWebsite($post['eventWebsite']);
            $event->setComments($post['comments']);

            $date = new DateTime();
            $mailHelper = new Helper_MailHelper();
            foreach ($admin as $adm)
                $mailHelper->sendMailBusinessComercial($adm, $event, $post['comments'], $date);

            $em->persist($event);
            $em->flush();
        }
    }

    public function autocompleteAction()
    {
        $this->view->setUseTemplate(false);
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        $company = array();

        $companies = $em->getRepository('DefaultDb_Entities_User')->findAllCompany();
        for ($i = 0; $i < count($companies); $i++)
        {
            $company[$i] = htmlentities($companies[$i]['company'] . '', ENT_QUOTES, 'ISO-8859-15');
            //$company[$i] = $companies[$i]['company'].'';  
        }
        $this->view->companies = $company;
    }
    
    public function bloquedUserAction()
    {
        
    }

}
