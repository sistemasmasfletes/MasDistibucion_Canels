<?php

class Admin_AdminUsersController extends Model3_Scaffold_Controller
{
    
    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }
        
    }
    public function __construct($request)
    {
        $this->_sc = new Scaffold_DefaultDb_User();
        parent::__construct($request);
    }

    public function indexAction()
    {
        $user = null;
        // obtenemos los usuarios activos usuario
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        $userRepository = $em->getRepository('DefaultDb_Entities_User');
        $user = $userRepository->findBy(array('status' => DefaultDb_Entities_User::STATUS_ACTIVE,
            'type'=> array( DefaultDb_Entities_User::USER_CLIENT, 
                            DefaultDb_Entities_User::USER_DRIVER,
                            DefaultDb_Entities_User::USER_SECRETARY
                )
            ));
        $this->view->users = $user;
    }

    public function addAction()
    {
        $this->view->getJsManager()->addJsVar('urlTypeClient', '\''.$this->view->getBaseUrl().'/Ajax/Users/typeClient'.'\'');
        $this->view->getJsManager()->addJsVar('changeR', '\''.$this->view->getBaseUrl().'/Ajax/Users/changeRoute'.'\'');
        $this->view->result =false;
        
        // para encriptar la contraseña en MD5
        if ($this->getRequest()->isPost())
        {
            $this->_post = $this->getRequest()->getPost();
            //var_dump($this->_post['dayInvoice']); Pendiente el post del dia de facturacion 
            if (isset($this->_post['password']) && $this->_post['password'] != '')
            {
                $this->_post['password'] = md5($this->_post['password']);
            }
            if($this->_post['type'] != DefaultDb_Entities_User::USER_CLIENT)
            {
                $this->_post['commercialName']='';
                $this->_post['category'] = '';
                $this->_post['point'] = '';
                $this->_post['dayInvoice'] = '';
                //unset($this->_post['commercialName']);
                //unset($this->_post['category']);
                //unset($this->_post['point']);
                //unset($this->_post['dayInvoice']);
            }
        }
        $this->_post['point']='';
        $this->view->getJsManager()->addJs('view/scripts/Admin/AdminUsers/add.js');
        if ($this->getRequest()->isPost())
        {
            $dbs = Model3_Registry::getInstance()->get('databases');
            $em = $dbs['DefaultDb'];
            /* @var $em Doctrine\ORM\EntityManager */
            $userAdapter = $em->getRepository('DefaultDb_Entities_User');
            $user = $userAdapter->findBy(array('username' => $this->_post['username']));
            if($user)
            {
                $this->view->result = 2;
            }
            if($this->view->result === false)
            {
                $user = $userAdapter->findBy(array('code' => $this->_post['code']));
                if($user)
                {
                    $this->view->result = 3;
                }
            }
            if(!$user)
            {
                parent::addAction();
            }
        }
    }

     public function deleteAction()
    {
        $user = null;
        $idUser = $this->getRequest()->getParam('id');
        if ($idUser  != null && $idUser != '' && is_numeric($idUser))
        {
            // obtenemos el usuario
            $dbs = Model3_Registry::getInstance()->get('databases');
            $em = $dbs['DefaultDb'];
            /* @var $em Doctrine\ORM\EntityManager */
            $userAdapter = $em->getRepository('DefaultDb_Entities_User');
            $user = $userAdapter->find($idUser);
            /**
             * @todo Verificar que el producto me pertenezca
             * Hacemos el Borrado logico 
             */
            $user->setStatus(2);
            $em->persist($user);
            $em->flush();
        }

        $this->view->user = $user;
    }
    
    public function editAction()
    {
        $this->view->getJsManager()->addJsVar('urlTypeClient', '\''.$this->view->getBaseUrl().'/Ajax/Users/typeClient'.'\'');
        $this->view->getJsManager()->addJsVar('changeR', '\''.$this->view->getBaseUrl().'/Ajax/Users/changeRoute'.'\'');
        $this->view->getJsManager()->addJs('view/scripts/Admin/AdminUsers/add.js');
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        $routeAdapter = $em->getRepository('DefaultDb_Entities_Route');
        $routePointAdapter = $em->getRepository('DefaultDb_Entities_RoutePoint');
        $categoryAdapter = $em->getRepository('DefaultDb_Entities_Category');
        $this->view->result = false;
        
        $user = null;
        $idUser = $this->getRequest()->getParam('id');
        if ($idUser  != null && $idUser != '' && is_numeric($idUser))
        {
            /* @var $em Doctrine\ORM\EntityManager */
            $userAdapter = $em->getRepository('DefaultDb_Entities_User');
            $user = $userAdapter->find($idUser);
            
            if ($this->getRequest()->isPost())
            {
                $this->_post = $this->getRequest()->getPost();
                if(isset($this->_post['idroutes']))
                {
                    foreach($this->_post['idroutes']  as $key => $idroute)
                    {
                        if($this->_post['point'][$key])
                        {
                            $branches = $em->getRepository('DefaultDb_Entities_BranchesUser');
                            $branch = $branches->find($idroute);
                            $point  = $em->find('DefaultDb_Entities_Point', $this->_post['point'][$key]);
                            if($point)
                            {
                                $branch->setPoint($point);
                                $em->persist($branch);
                            }
                        }
                    }
                }
                unset($this->_post['idroutes']);
                unset($this->_post['point']);
                unset ($this->_post['route']);
                $this->_post['idroutes']=0;
                $this->_post['point']=0;
                $this->_post['route']=0;
                if (isset($this->_post['password']) && $this->_post['password'] != '')
                {
                    $this->_post['password'] = md5($this->_post['password']);
                }
                $userPost = $userAdapter->findOneBy(array('username'=>$this->_post['username']));
                if($userPost && $userPost->getId()!=$user->getId())
                {
                    $this->view->result=2;
                }
                else
                {
                    $userPost = $userAdapter->findOneBy(array('code'=>$this->_post['code']));
                    if($userPost && $userPost->getId()!=$user->getId())
                    {
                        $this->view->result = 3;
                    }
                }
            }
            
        }

        $this->view->user = $user;
        $this->view->category = $categoryAdapter->findAll();
        $this->view->routes = $routeAdapter->findAll();
        $this->view->routePoints = $routePointAdapter->findAll();
        $this->view->rootOfUser = null;
        $this->view->pointOfRoot = null;
        if($this->view->routePoints)
        {
            foreach ($this->view->routePoints as $rP)
            {
                if($user->getType() == DefaultDb_Entities_User::USER_CLIENT)
                {
                    //Realizar cambios
                    $this->view->pointOfRoot = $routePointAdapter->findBy(array('route'=>($this->view->rootOfUser)));
                }
            }
            
        }
        if($this->view->result === false)
        {
            parent::editAction();
        }
    }

}