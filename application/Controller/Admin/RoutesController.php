<?php

class Admin_RoutesController extends Model3_Scaffold_Controller
{
    public function __construct($request)
    {
        $this->_sc = new Scaffold_DefaultDb_Route();
        parent::__construct($request);
    }
    
    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }  
    }
    public function indexAction()
    {
        
        $em = $this->getEntityManager('DefaultDb');
        $routeRepos = $em->getRepository('DefaultDb_Entities_Route');
        $routes = $routeRepos->findBy(array('status'=>1));

        $this->view->routes = $routes;
    }
    
    public function pointsAction()
    {
        $routeId = $this->getRequest()->getParam('id');
        if(is_null($routeId) == false)
        {
            $em = $this->getEntityManager('DefaultDb');

            $rpRepos = $em->getRepository('DefaultDb_Entities_RoutePoint');
            $routeRepos = $em->getRepository('DefaultDb_Entities_Route');
            
            $routePoints = $rpRepos->getRoutesPointsByRoute($routeId);
            $route = $routeRepos->findOneById($routeId);
            
            $this->view->route = $route;
            $this->view->routePoints = $routePoints;
        }
        
        $this->view->getCssManager()->addCss('view/scripts/Admin/Routes/points.css');
    }
    
    public function newPointAction()
    {
        $routeId = $this->getRequest()->getParam('id');  
       
        if(is_null($routeId) == true)
        {
            $this->redirect('Admin/Routes');
        }
        else
        {
            $em = $this->getEntityManager('DefaultDb');
            $pointRepository = $em->getRepository('DefaultDb_Entities_Point');
            $rpRepository = $em->getRepository('DefaultDb_Entities_RoutePoint');
            $routeRepos = $em->getRepository('DefaultDb_Entities_Route');
            $route = $routeRepos->findOneById($routeId);
            
            
            $rpRepository->getStepUpRoutePoint(1, 3);
                
            $salePoints = $pointRepository->findBy(array('type' => DefaultDb_Entities_Point::TYPE_SALE_POINT));
            $exchangeCenters = $rpRepository->getRoutesPointsByRouteAndPointType($routeId, DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER);
            $pointAdapter=$em->getRepository('DefaultDb_Entities_Point');
            $allexchangeCenters = $pointAdapter->findBy(array('type'=> DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER));
            $states = $em->getRepository('DefaultDb_Entities_State')->findAll();
            $this->view->states = $states;
            $this->view->route = $route;
            $this->view->salePoints = $salePoints;
            $this->view->exchangeCenters = $exchangeCenters;
            $this->view->allexchangeCenters = $allexchangeCenters;
            $this->view->scaffoldPoint = new Scaffold_DefaultDb_Point();
            
            $this->view->getJsManager()->addJs('view/scripts/Admin/Routes/action.js');
            $newRoutePoint = array(
                'module' => 'Admin',
                'controller' => 'Routes',
                'action' => 'ajaxNewRoutePoint'
            );
            $this->view->getJsManager()->addJsVar('newRoutePoint', '"'.$this->view->url($newRoutePoint).'"');
            $this->view->getJsManager()->addJsVar('urlRechargeInterchangeCenter', '"'.$this->view->url(array('controller'=>'Routes','action'=>'axGetInterchangeExist')).'"');
            $this->view->getJsManager()->addJsVar('routeId', $routeId);
        }
    }
    
    public function ajaxNewRoutePointAction()
    {
        $this->result = false;
        $this->view->setUseTemplate(false);
        if($this->getRequest()->isPost() == true)
        {
            $post = $this->getRequest()->getPost();
            if(array_key_exists('routeId', $post) == true)
            {
                $em = $this->getEntityManager('DefaultDb');                
                switch($post['creationType'])
                {
                    //creacion del punto de ruta basado en un punto nuevo
                    case 1:
                        //creacion de nuevo punto de venta/centro de intercambio
                        $point = new DefaultDb_Entities_Point;
                        $point->setCode($post['pointCode']);
                        $point->setName($post['pointName']);
                        $point->setType($post['pointType']);
                        $point->setStatus(DefaultDb_Entities_Point::STATUS_NORMAL);
                        $point->setAddress($post['pointAddress']);
                        $state = $em->getRepository('DefaultDb_Entities_State')->find($post['state']);
                        $point->setState($state);
                        
                        /**
                         * Guardamos el branch en el usuario de tipo Cliente_Mas_Distribucion
                         */
                        $branch = new DefaultDb_Entities_BranchesUser;
                        $client = $em->getRepository('DefaultDb_Entities_User')->findOneByType(DefaultDb_Entities_User::USER_CLIENT_MAS_DISTRIBUCION); 
                        if($client)
                        {
                            $branch->setClient($client);
                            $branch->setDirection($post['pointAddress']);
                            $branch->setName($post['pointName']);
                            $branch->setPoint($point);
                            $em->persist($branch);
                        }
                        break;
                    case 4:
                    case 2: //creacion con respecto a la lista de puntos de venta
                    case 3: //creacion con respecto a la lista de centros de intercambio
                        $point = $em->getRepository('DefaultDb_Entities_Point')->findOneBy(array('id' => $post['pointId']));
                        break;
                }                
                //obtencion de la ruta
                $route = $em->getRepository('DefaultDb_Entities_Route')->findOneBy(array('id' => $post['routeId']));
                //creacion del punto de ruta
                $routePoint = new DefaultDb_Entities_RoutePoint;
                $routePoint->setRoute($route);
                $order = $em->getRepository('DefaultDb_Entities_RoutePoint')->getLastOrderNumberByRoute($route);
                $order ++;
                $routePoint->setOrder($order);//obtener el ultimo
                $routePoint->setPoint($point);
                $routePoint->setStatus(DefaultDb_Entities_RoutePoint::STATUS_NORMAL);
                $arrivalTime = new DateTime();
                $arrivalTime->setTime(0, $post['routePointArrivalTime'], 0);
                $routePoint->setArrivalTime($arrivalTime);
                
                $em->persist($routePoint);
                $em->flush();
                $result = true;
            }
        }
        $this->view->result = $result;
    }
    
    public function stepUpRoutePointAction()
    {
        $params = $this->getRequest()->getParams();
        if(is_array($params) == true && array_key_exists('id', $params) == true 
            && array_key_exists('routePointId', $params) == true)
        {
            //obtener el punto de ruta de a subir
            $em = $this->getEntityManager('DefaultDb');
            $routePointToStepUp = $em->getRepository('DefaultDb_Entities_RoutePoint')->find($params['routePointId']);
            
            //obtener el punto de ruta a bajar
            $routePointToStepDown = $em->getRepository('DefaultDb_Entities_RoutePoint')->getStepUpRoutePoint($params['id'], $routePointToStepUp->getOrder());
            if($routePointToStepDown instanceof DefaultDb_Entities_RoutePoint == true 
                && $routePointToStepDown->getId() != $routePointToStepUp->getId())
            {
                //guardar el valor del orden del punto a bajar en una variable temporal
                $tmpOrder = $routePointToStepDown->getOrder();

                //establecer nuevos valores de orden
                $routePointToStepDown->setOrder($routePointToStepUp->getOrder());
                $routePointToStepUp->setOrder($tmpOrder);

                //actualizar db
                $em->persist($routePointToStepDown);
                $em->persist($routePointToStepUp);
                $em->flush();
            }
            $this->redirect('Admin/Routes/points/id/'.$params['id']);
        }
        else
            $this->redirect('Admin/Routes');
    }
    
    public function stepDownRoutePointAction()
    {
        $params = $this->getRequest()->getParams();
        if(is_array($params) == true && array_key_exists('id', $params) == true 
            && array_key_exists('routePointId', $params) == true)
        {
            //obtener el punto de ruta de a subir
            $em = $this->getEntityManager('DefaultDb');
            $routePointToStepDown = $em->getRepository('DefaultDb_Entities_RoutePoint')->find($params['routePointId']);
            
            //obtener el punto de ruta a bajar
            $routePointToStepUp = $em->getRepository('DefaultDb_Entities_RoutePoint')->getStepDownRoutePoint($params['id'], $routePointToStepDown->getOrder());
            
            if($routePointToStepUp instanceof DefaultDb_Entities_RoutePoint == true 
                && $routePointToStepUp->getId() != $routePointToStepDown->getId())
            {
                //guardar el valor del orden del punto a subir en una variable temporal
                $tmpOrder = $routePointToStepUp->getOrder();

                //establecer nuevos valores de orden
                $routePointToStepUp->setOrder($routePointToStepDown->getOrder());
                $routePointToStepDown->setOrder($tmpOrder);

                //actualizar db
                $em->persist($routePointToStepDown);
                $em->persist($routePointToStepUp);
                $em->flush();
            }
            $this->redirect('Admin/Routes/points/id/'.$params['id']);
        }
        else
            $this->redirect('Admin/Routes');
    }
    
    public function deleteRoutePointAction()
    {
        $params = $this->getRequest()->getParams();
        if(is_array($params) == true && array_key_exists('id', $params) == true 
            && array_key_exists('routePointId', $params) == true)
        {
            //obtener el punto de ruta de a subir
            $em = $this->getEntityManager('DefaultDb');
            $routePointToDelete = $em->getRepository('DefaultDb_Entities_RoutePoint')->find($params['routePointId']);
           
            $routePointToDelete->setStatus(0);
           // $em->remove($routePointToDelete);
            $em->persist($routePointToDelete);
            $em->flush();
            $this->redirect('Admin/Routes/points/id/'.$params['id']);
        }
        else
            $this->redirect('Admin/Routes');
    }
    
    public function editRoutePointAction()
    {
        $params = $this->getRequest()->getParams();
        if(is_array($params) == true && array_key_exists('id', $params) == true 
            && array_key_exists('routePointId', $params) == true)
        {
            //obtener el punto de ruta de a subir
            $em = $this->getEntityManager('DefaultDb');
            $routePointToEdit = $em->getRepository('DefaultDb_Entities_RoutePoint')->find($params['routePointId']);
            $this->view->routePoint = $routePointToEdit;
            $states = $em->getRepository('DefaultDb_Entities_State')->findAll();
            $this->view->states = $states;
        }
        else
            $this->redirect('Admin/Routes');
    }
    
    public function saveRoutePointAction()
    {
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            if(array_key_exists('routePointId', $post) == true)
            {
                $em = $this->getEntityManager('DefaultDb');
                $rp = $em->getRepository('DefaultDb_Entities_RoutePoint')->find($post['routePointId']);
                /* @var $rp DefaultDb_Entities_RoutePoint */
                $rp->getPoint()->setCode($post['code']);
                $rp->getPoint()->setName($post['name']);
                $rp->getPoint()->setType($post['type']);
                $rp->getPoint()->setAddress($post['address']);
                $state = $em->getRepository('DefaultDb_Entities_State')->find($post['state']);
                $rp->getPoint()->setState($state);
                $arrivalTime = new DateTime;
                $arrivalTime->setTime(0, $post['arrivalTime'], 0);
                $rp->setArrivalTime($arrivalTime);
                $em->flush();
                $this->view->routePoint = $rp;
            }
        }
    }

    public function deleteAction()
    {
       $idRoute = $this->getRequest()->getParam('id');
       $em = $this->getEntityManager('DefaultDb');
       $routesRepository = $em->getRepository('DefaultDb_Entities_Route');
       $route = $routesRepository->find($idRoute);
       
       $route->setStatus(0);
       $em->persist($route);
       $em->flush();
    }
    
}