<?php

class User_AjaxCartController extends Model3_Controller
{

    protected $cart;
    protected $commerce;

    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }
        $this->commerce = new Model3_Session_Namespace('commerce');
        //Si dentro del namespace no existe la propiedad "cart" creamos un nuevo objeto carrito
        //de lo contrario asignamos el objeto contenido en la sesion a una variable.
        if(!$this->commerce->cart)
        {
            $this->cart = new Class_ShoppingCart();
        }
        else
        {
            $this->cart = $this->commerce->cart;
        }
    }

    public function indexAction()
    {
        
    }

    public function addShippingCostAction()
    {
        $this->view->setUseTemplate(false);
        $this->view->vista = NULL;

        if($this->cart != null && $this->commerce != null)
        {
            $post = $this->getPost();
            $this->cart->setShippingCost($post['cost']);
            $this->cart->setIdShipping($post['idEnvio']);
            $this->view->paymentAmount = $this->cart->getPaymentAmount();
        }
    }

    public function botonContactAction()
    {
        $this->view->setUseTemplate(false);
        //Se envia un correo de aviso por la creacion de un nuevo comentario
        if($this->isPost())
        {
            $post = $this->getPost();
            $sitiosAdapter = new Model_Sitios();
            $sitio = $sitiosAdapter->getSiteById($post['idSitio']);
            $sitioVista = $sitiosAdapter->getSiteById($post['idSitio'], SELECTOR);

            $correoContacto1 = '';
            $correoContacto2 = '';

            //Si esta habilitado un correo exclusivo para los comentarios, ya sea en la vista o en la edicion,
            //se enviara el correo a este.
            if((strlen($sitio['mail_comentarios']) > 0) || (strlen($sitioVista['mail_comentarios']) > 0))
            {
                //Seleccionamos preferentemente el correo del sitio en edicion, o en su
                //defecto, el de vista
                if(strlen($sitio['mail_comentarios']) > 0)
                {
                    $correoContacto1 = $sitio['mail_comentarios'];
                }
                else
                {
                    $correoContacto1 = $sitioVista['mail_comentarios'];
                }
            }
            else
            {
                //Si no se tiene configurado el correo de comentarios, se envian a los correos
                //principal y alternativos del usuario

                $helperDB = new Helper_Database();
                $db = $helperDB->inicializaDB('db_common');
                $userAdapter = new Model_Users($db);
                $idUsuario = $sitio['id_usuario'];
                $usuario = $userAdapter->getUser($idUsuario, FETCH_OBJECT);

                $correoContacto1 = $usuario->mail_principal;
                $correoContacto2 = $usuario->mail_alternativo;
            }

            $urlSitio = (strlen($sitio['dominio']) > 0) ? $sitio['dominio'] : 'http://' . $sitio['nombre_fix'] . '.storesfactory.com';
            $subjectMail = 'Nuevo comentario en el sitio ' . $sitio['dominio'] . '';
            $message = utf8_decode($post['name']) . ' (' . $post['email'] . ') ha enviado un email en <a href="' . $urlSitio . '">' . $sitio['nombre'] . '</a> : ';
            $message .= '<br/><br/>';
            $message .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . utf8_decode($post['comentario']);
            $message .= '<br/><br/>';

            //Se envian notificaciones de nuevos comentarios recibidos en el sitio
            $helperMail = new Helper_Mailer();
            (strlen($correoContacto1) > 0) ? $helperMail->avisosAction($message, $correoContacto1, $subjectMail) : '';
            (strlen($correoContacto2) > 0) ? ( $correoContacto1 != $correoContacto2 ? $helperMail->avisosAction($message, $correoContacto2, $subjectMail) : '' ) : '';
            $this->view->res = 1;
        }
    }

    public function addToCartAction()
    {
        $this->view->setUseTemplate(false);
        $post = $this->getRequest()->getPost();
        if(count($post)<=0){
             $post =$this->getRequest()->getPostJson();
        }
        $em = $this->getEntityManager('DefaultDb');        if(isset($post['arrayp'])){        	         	$predoduct = $post['arrayp'];                	foreach ($predoduct as $precheck){        		$product = $em->find('DefaultDb_Entities_Product', intval($precheck));        		if($this->cart->add_item($product)){        			$this->view->result = true;        		}else{        			$this->view->result = false;        		}        	}        }else{        	         	        
	        $product = $em->find('DefaultDb_Entities_Product', $post['id']);	
	        if(isset($post['idVariant']))	
	        {	
	//            if ($post['varianteProducto'] != 0)	
	//        {	
	//            $variantesAdapter = new Data_ProductosVariantes();	
	//            $variante = $variantesAdapter->findOne('id', $post['varianteProducto']);	
	//            $post['itemName'] = $post['itemName'] . ' - ' . $variante->descripcion;	
	//        }	
	            $variant = $em->find('DefaultDb_Entities_ProductVariants', $post['idVariant']);	
	            if($this->cart->add_item($product,$variant))	
	            {	
	                $this->view->result = true;	
	            }	
	            else	
	            {	
	                $this->view->result = false;	
	            }	
	        }	
	        else	
	        {	
	            //Llamanos al metodo para agregar elementos al carrito	
	            if($this->cart->add_item($product))	
	            {	
	                $this->view->result = true;	
	            }	
	            else	
	            {	
	                $this->view->result = false;	
	            }	
	        }
        }
        //Asignamos el carrito a la propiedad 'cart' del namespace commerce
        $this->commerce->cart = $this->cart;
    }

    public function validateQtyAction()
    {
        $this->view->setUseTemplate(false);
        $post = $this->getPost();
        $productAdapter = new Data_Productos();
        $producto = $productAdapter->findOne('id', $post['idProducto']);
        $qty = (int) $post['qty'];

        if($post['varianteProducto'] != 0)
        {
            $variantesAdapter = new Data_ProductosVariantes();
            $idVariante = $post['varianteProducto'];
            $variante = $variantesAdapter->findOne('id', $idVariante);
            $existencias = $variante->existencias;
        }
        else
        {
            $existencias = $producto->existencias;
        }
        $existencias_en_carrito = 0;
        //Si estamos agregando desde el boton, solo se incrementa en 1, por lo que le sumamos lo que hay en el carito
        if(isset($post['add']))
        {
            //Revisamos la cantidad en el carrito
            $qtyArray = $this->cart->getItemQtys();
            if(isset($qtyArray[$producto->id . '-' . $post['varianteProducto']]))
            {
                $existencias_en_carrito = $qtyArray[$producto->id . '-' . $post['varianteProducto']];
            }
        }

        $this->view->res = 1;
        if($existencias == -1)
            $this->view->res = 1;
        else
        {
            //Sumamos la cantidad en el carrito y la solicitada para compararla con las existencias
            if($existencias < ($existencias_en_carrito + $qty))
                $this->view->res = 0;
        }
    }

    public function validateRoomAction()
    {
        if($this->isPost())
        {
            $this->view->setUseTemplate(false);
            $post = $this->getPost();
            $roomAdapter = new Model_Reservaciones_Habitaciones();
            $room = $roomAdapter->findOne('id', $post['idRoom']);
            $qty = (int) $post['qty'];


            $existencias = $room->num_habitaciones;

            $existencias_en_carrito = 0;


            $this->view->res = 1;
            if($existencias == -1)
                $this->view->res = 1;
            else
            {
                //Sumamos la cantidad en el carrito y la solicitada para compararla con las existencias
                if($existencias < ($existencias_en_carrito + $qty))
                    $this->view->res = 0;
            }
        }
    }

    public function addRoomToCartAction()
    {
        $this->view->setUseTemplate(false);
        if($this->cart != null && $this->commerce != null)
        {
            $post = $this->getPost();
            $edit = 0;
            if(isset($post['itemAddEdit']) && ($post['itemAddEdit'] > 0))
                $edit = $post['itemAddEdit'];
            //Llamanos al metodo para agregar habitaciones al carrito        
            if(isset($post['costoAdditionalAdults']) || isset($post['additionalAdults']))
                $this->cart->add_item_of_room($post['itemId'], $post['itemPrice'], $post['itemName'], $post['itemQty'], 0, $post['img'], $post['costoAdditionalAdults'], $post['additionalAdults'], $post['costoAdditionalChilds'], $post['additionalChilds'], $post['adults'], $post['child'], $edit);
            else
                $this->cart->add_item_of_room($post['itemId'], $post['itemPrice'], $post['itemName'], $post['itemQty'], 0, $post['img'], 0, 0, 0, 0, $post['adults'], $post['child'], $edit);
            //Asignamos el carrito a la propiedad 'cart' del namespace commerce
            $this->view->reservaciones = true;
            $this->commerce->cart = $this->cart;
        }
    }

    /*
     * Actualiza la cantidad de adicionales con respecto al costo
     */

    public function updateQtyRoomAction()
    {
        $this->view->setUseTemplate(false);
        if($this->cart != null && $this->commerce != null)
        {
            $post = $this->getPost();
            $res = $this->cart->update_item($post['item_id'], $post['item_qty']);
            $this->commerce->cart = $this->cart;
            $this->view->reservaciones = true;
        }
    }

    public function updateQtyAction()
    {
        $this->view->setUseTemplate(false);
        if($this->cart != null && $this->commerce != null)
        {
            $em = $this->getEntityManager('DefaultDb');
            $post = $this->getRequest()->getPost();
            $variant = null;
            if(strpos($post['item_id'],"_")!==false)
            {
                $item_variant = explode ('_',$post['item_id'] );
                $itemId = $item_variant[0];
                $variantId = $item_variant[1];
                $product = $em->find('DefaultDb_Entities_Product', $itemId);
                $variant = $em->find('DefaultDb_Entities_ProductVariants',$variantId);
            }
            else
                $product = $em->find('DefaultDb_Entities_Product', $post['item_id']);
            
            //$res = $this->cart->update_item($post['item_id'] . '-' . $post['varianteProducto'], $post['item_qty']);
            if($this->cart->update_item($product, $post['item_qty'],$variant))
            {
                $this->view->result = true;
                $this->view->subtotal = '$'.number_format($this->cart->getSubtotal(), 2);
                $this->view->subtotalProduct = '$'.number_format($this->cart->getPriceItem($product) * $this->cart->getQtyItem($product,$variant), 2);
            }
            else
            {
                $this->view->result = false;
            }
            $this->commerce->cart = $this->cart;
        }
    }

    public function removeFromCartAction()
    {
        $this->view->setUseTemplate(false);
        if($this->cart != null && $this->commerce != null)
        {
            $em = $this->getEntityManager('DefaultDb');
            $post = $this->getRequest()->getPost();
            $variant = null;
            if(strpos($post['item_id'],"_")!==false)
            {
                $item_variant = explode ('_',$post['item_id'] );
                $itemId = $item_variant[0];
                $variantId = $item_variant[1];
                $product = $em->find('DefaultDb_Entities_Product', $itemId);
                $variant = $em->find('DefaultDb_Entities_ProductVariants',$variantId);
            }
            else
                $product = $em->find('DefaultDb_Entities_Product', $post['item_id']);

            if($this->cart->del_item($product,$variant))
            {
                $this->view->result = true;
                $this->view->subtotal = '$'.number_format($this->cart->getSubtotal(), 2);
            }
            else
            {
                $this->view->result = false;
            }
            //Asignamos el carrito a la propiedad 'cart' del namespace commerce
            if(!$this->cart->getItemCount())
                $this->cart=false;
            $this->commerce->cart = $this->cart;
        }
    }

    public function removeRoomFromCartAction()
    {
        $this->view->setUseTemplate(false);
        if($this->cart != null && $this->commerce != null)
        {
            $post = $this->getPost();
            //Eliminamos el articulo del carrito
            $this->cart->del_item($post['itemId']);
            //Asignamos el carrito a la propiedad 'cart' del namespace commerce
            $this->view->reservaciones = true;
            $this->commerce->cart = $this->cart;
        }
    }

    public function emptyCartAction()
    {
        $this->view->setUseTemplate(false);
        if($this->cart != null && $this->commerce != null)
        {
            $this->cart->empty_cart();
            //Asignamos el carrito a la propiedad 'cart' del namespace commerce
            $this->cart=false;
            $this->commerce->cart = $this->cart;
            $this->view->result=true;
        }
    }

    public function updateTotalAction()
    {
        $this->view->setUseTemplate(false);
        if($this->isPost())
        {
            $post = $this->getPost();
            $payForm = $post['payForm'];
            if($payForm == 'contraEntrega')
            {
                $this->cart->setShippingCost(0);
            }
            $this->view->pagoContraEntrega = $post['pagoContraEntrega'];
        }
    }

    public function updateTotalReservationAction()
    {
        $this->view->setUseTemplate(false);
        $moduloReservAdapter = new Model_Reservaciones();
        if($this->isPost())
        {

            $post = $this->getPost();

            $tienda = $moduloReservAdapter->findOne('id_sitio', $post['idSitio']);
            $payForm = $post['payForm'];
            if($payForm == 'contraEntrega')
            {
                $this->cart->setShippingCost(0);
            }
            $this->view->pagoContraEntrega = $post['pagoContraEntrega'];
            $this->view->reservaciones = true;
            $this->view->configTienda = $tienda;
        }
    }

    public function insertOrderAction()
    {
        $this->view->setUseTemplate(false);
        $this->view->res = 'false';
        if($this->isPost())
        {
            $sitio = $this->view->sitio;
            $post = $this->getPost();
            $ordersAdapter = new M3Commerce_Adapter_Orders();
            $data['client_id'] = $this->cart->getUserId();
            $data['site_id'] = $this->cart->getSiteId();
            $data['total_amount'] = $this->cart->getPaymentAmount();
            $data['buy_date'] = date('Y-m-d H:i:s');
            $data['id_forma_entrega'] = $this->cart->getIdShipping();
            $data['status'] = ORDEN_POR_PAGAR;
            $data['comments'] = $post['comments'];
            //se establece a dineromail como metodo de pago por que es el unico que inserta por ajax la orden
            $data['payment_method'] = DINEROMAIL;
            $data['email'] = $post['email'];
            $data['tel'] = $post['tel'];
            $data['name'] = $post['name'];
            $data['street1'] = $post['street1'];
            $data['city'] = $post['city'];
            $data['state'] = $post['state'];
            $data['zipcode'] = $post['zipcode'];

            if($ordersAdapter->insert($data))
            {
                $orderId = $ordersAdapter->insertId();
                $productsToOrdersAdapter = new M3Commerce_Adapter_ProductsToOrders();
                $variantsAdapter = new Data_ProductosVariantes();
                $productos = $this->cart->mergeProductData();

                //llenamos gateways data antes de mandar la info por si se interrumpe
                $status = 1; //pending
                $processOrder = new M3Commerce_Class_SalesProcess();
                $formasEntregaAdapter = new Data_FormasEntrega();
                $formaEntrega = $formasEntregaAdapter->findWhere('id = ' . $this->cart->getIdShipping());
                $formaEntrega = (is_array($formaEntrega) && count($formaEntrega) > 0) ? $formaEntrega[0] : false;
                $priceShippingMethod = ($formaEntrega == false) ? 0 : $this->cart->getShippingCost();
                $shippingMethod = ($formaEntrega == false) ? 'Forma de envio no encontrada' : $formaEntrega->nombre;
                $processOrder->gatewayData($orderId, '', $status, $this->cart->getPaymentAmount(), $priceShippingMethod, $shippingMethod, DINEROMAIL);

                $ordersAdapter->updateOrderByOrderId($orderId);

                $salesAdapter = new M3Commerce_Class_SalesProcess();
                $salesAdapter->updateOrderIdBySite($sitio['id'], $orderId);

                foreach($productos as $product)
                {
                    $product_array = array();
                    $product_array['product_id'] = $product['id'];
                    $product_array['order_id'] = $orderId;
                    $product_array['quantity'] = $product['qty'];
                    $product_array['price'] = $product['price'];
                    $product_array['final_price'] = $product['price'];
                    $product_array['variante'] = '';
                    $product_array['id_variante'] = $product['variantId'];
                    if($product['variantId'] != 0)
                    {
                        $variante = $variantsAdapter->findOne('id', $product['variantId']);
                        if(is_object($variante))
                            $product_array['variante'] = $variante->descripcion;
                    }
                    $productsToOrdersAdapter->insert($product_array);
                }
                $this->view->res = base64_encode($orderId);
            }
        }
    }

}
?>

