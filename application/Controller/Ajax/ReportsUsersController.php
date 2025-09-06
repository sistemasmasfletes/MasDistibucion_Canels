<?php

class Ajax_ReportsUsersController extends Model3_Controller
{
    public function init()
    {
        $this->view->setUseTemplate(false);
    }
    
    public function indexAction()
    {
        
    }

    public function searchAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $usersAdapter = $em->getRepository('DefaultDb_Entities_User');
        
        $response = new stdClass();
        $response->res = false;
        $response->message = 'No se ha encontrado el usuario solicitado.';

        if($this->getRequest()->getPost())
        {
            $post = $this->getRequest()->getPost();
            $user = $usersAdapter->findOneBy(array('username'=>$post['username']));

            if($user instanceof DefaultDb_Entities_User)
            {
                $response->res = true;
                $response->message = 'Usuario encontrado';
                $response->userId = $user->getId();
            }
        }

        $this->view->response = json_encode($response);
    }

    public function detailsAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $usersAdapter = $em->getRepository('DefaultDb_Entities_User');
        $user = false;

        if($this->getRequest()->getPost())
        {
            $post = $this->getRequest()->getPost();
            $user = $usersAdapter->findOneBy(array('id'=>$post['currentUser']));
        }
        $this->view->user = $user;
    }

    public function hitoricalOrdersAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $usersAdapter = $em->getRepository('DefaultDb_Entities_User');
        $ordersAdapter = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
        $user = false;

        if($this->getRequest()->getPost())
        {
            $post = $this->getRequest()->getPost();
            $user = $usersAdapter->findOneBy(array('id'=>$post['currentUser']));

            if($user instanceof DefaultDb_Entities_User)
            {
                $this->view->buyerOrders = $ordersAdapter->findBy(array('buyer'=>$user));
                $this->view->sellerOrders = $ordersAdapter->findBy(array('seller'=>$user));
            }
        }
        $this->view->user = $user;
    }

    public function getOrderDetailsAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $ordersAdapter = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
        $order = false;

        if($this->getRequest()->getPost())
        {
            $post = $this->getRequest()->getPost();
            $order = $ordersAdapter->findOneBy(array('id'=>$post['idOrder']));
        }

        $this->view->order = $order;
    }

    public function catalogsAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $usersAdapter = $em->getRepository('DefaultDb_Entities_User');
        $catalogsAdapter = $em->getRepository('DefaultDb_Entities_Catalog');
        $user = false;

        if($this->getRequest()->getPost())
        {
            $post = $this->getRequest()->getPost();
            $user = $usersAdapter->findOneBy(array('id'=>$post['currentUser']));

            if($user instanceof DefaultDb_Entities_User)
            {
                $catalogs = $catalogsAdapter->findBy(array('client'=>$user));
                $this->view->catalogs = $catalogs;
            }
        }

        $this->view->user = $user;
    }

    public function changeStatusProductAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $productsAdapter = $em->getRepository('DefaultDb_Entities_Product');

        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            if(isset($post['pId']) && isset($post['status']))
            {
                $prod = $productsAdapter->find($post['pId']);
                $prod->setStatus($post['status']);

                $em->flush();
            }
        }
    }

    public function blockUserAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $usersAdapter = $em->getRepository('DefaultDb_Entities_User');
        $changeStatusAdapter = $em->getRepository('DefaultDb_Entities_ChangeStatus'); 
        $this->view->comments = null;
        $user = false;

        if($this->getRequest()->getPost())
        {
            $post = $this->getRequest()->getPost();
            if(isset($post['currentUser']) && $post['currentUser'] != "")
            {
                $user = $usersAdapter->findOneBy(array('id'=>$post['currentUser']));
                $changeStatus = $changeStatusAdapter->getLastFiveComment($user);
                $this->view->comments = $changeStatus;
            }
        }
        
        $this->view->user = $user;
    }

    public function changeStatusUserAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $usersAdapter = $em->getRepository('DefaultDb_Entities_User');
        $changeStatusAdapter = $em->getRepository('DefaultDb_Entities_ChangeStatus');

        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            if(isset($post['idUser']) && isset($post['status']) && isset($post['comment']))
            {
                $user = $usersAdapter->find($post['idUser']);
                $user->setStatus($post['status']);
                $em->persist($user);
                
                //Se introduce un comentario por cada cambio de estado
                $changeStatus = new DefaultDb_Entities_ChangeStatus();
                $changeStatus->setComment($post['comment']);
                $changeStatus->setDateChange(new DateTime());
                $changeStatus->setNewStatus($post['status']);
                $changeStatus->setUser($user);
                $em->persist($changeStatus);
                
                $em->flush();
                $comments = $changeStatusAdapter->getLastFiveComment($user);
                $this->view->comments = $comments;
            }
        }
    }
    
    public function invoicesUserAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $usersAdapter = $em->getRepository('DefaultDb_Entities_User');
        $invoicesAdapter = $em->getRepository('DefaultDb_Entities_Invoices');
        $user = false;

        if($this->getRequest()->getPost())
        {
            $post = $this->getRequest()->getPost();
            $user = $usersAdapter->findOneBy(array('id'=>$post['currentUser']));

            if($user instanceof DefaultDb_Entities_User)
            {
                $invoices = $invoicesAdapter->findBy(array('client' => $user));
                $this->view->invoices = $invoices;
            }
        }

        $this->view->user = $user;
        
    }
    
    public function changeStatusInvoicesAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $invoicesRepos = $em->getRepository('DefaultDb_Entities_Invoices');

        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            if(isset($post['oId']) && isset($post['status']))
            {
                $invoice = $invoicesRepos->find($post['oId']);
                $invoice->setStatus($post['status']);

                $em->flush();
            }
        }
    }
    
    public function branchesUserAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $branchesRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
        $pointsRepos = $em->getRepository('DefaultDb_Entities_Point');
        $statesRepos = $em->getRepository('DefaultDb_Entities_State');
        $this->view->states = $statesRepos->findAll();

        

        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $branches = $branchesRepos->findBy(array('client' => $post['currentUser']));
            $points = $pointsRepos->findAll();
            $this->view->branches = $branches;
            $this->view->points = $points;
        }
        
    }
    
    public function saveChangeBrancheAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $branchesRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
        $pointsRepos = $em->getRepository('DefaultDb_Entities_Point');
        $statesRepos = $em->getRepository('DefaultDb_Entities_State');
        
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
			$branchepoint = $branchesRepos->find($post['idPoint']);
            $point = $pointsRepos->find($branchepoint->getPoint()->getId());
            $branche = $branchesRepos->find($post['idBranche']);
            $points = $pointsRepos->findAll();
            $branche->setPoint($point);
            $em->persist($branche);
            $em->flush();            $correo = "<html><body>	            		<span>Hola ".$branche->getClient()->getFirstName()." ".$branche->getClient()->getLastName().",	            		<br />	            		es un gusto comunicarnos con usted para poder informarle que el alta de su ubicaci&oacute;n: ".$point->getName()." en nuestro portal ya quedo REALIZADO CON &Eacute;XITO.	            		<br >	            		".$point->getAddress()->getAddress().", ".$point->getExtNumber()." ".$point->getIntNumber().", ".$point->getNeighborhood().            	            		"<br />						podr&aacute;s ver los detalles ingresando masdistribucion.com con tu usuario y contrase&ntilde;a.	            		<br >						Muchas gracias, esperamos que pronto comparta su experiencia en nuestro portal.<br >						Tambi&eacute;n es importante que conozca las tiendas con las que contamos.<br >						Que tenga un excelente d&iactue;a.<br >						</body></html>";                        $mailsend = $this->sendMail($correo,"notificacionesmasdistribucion@gmail.com",$branche->getClient()->getMail()/*donde llega*/,"Aviso sucursal en ruta");                        $correo1 = "<html><body>	            		<span>Hola Controlador,	            		<br />	            		la sucursal ".$point->getName()." del cliente ".$branche->getClient()->getFirstName()." ".$branche->getClient()->getLastName()."	            		<br >	            		ha sido ingresada a una ruta exitosamente<br >						Que tenga un excelente d&iactue;a.<br >						</body></html>";                        $mailsend1 = $this->sendMail($correo1,"notificacionesmasdistribucion@gmail.com","notificacionesmasdistribucion@gmail.com"/*donde llega*/,"Aviso sucursal en ruta");                        
            
            $controleruser= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => 32));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
            
            $data = array(
            		'title' => 'Sucursal en Ruta',
            		'body' => 'Se ha agregado el punto del cliente '.$branche->getClient()->getFirstName()." ".$branche->getClient()->getLastName().' a una ruta'
            );
            
            $this->sendPushNotification($controleruser->getToken(),  $data);
            
            if($branche->getClient()->getToken() != ""){
	            $data1 = array(
	            		'title' => 'Sucursal en Ruta',
	            		'body' => 'Hola, te informamos que tu sucursal ya esta en ruta para recibir entregas'
	            );
	            $this->sendPushNotification($branche->getClient()->getToken(),  $data1);
            }
            
            $branches = $branchesRepos->findBy(array('client' => $post['currentUser']));
            $this->view->branches = $branches;
            $this->view->points =$points;
            $this->view->states = $statesRepos->findAll();
        }
        
        
    }
    
}               