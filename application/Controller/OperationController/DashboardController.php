<?php

/**
 * Description of DashboardController
 *
 * @author Nylaye
 */
class OperationController_DashboardController extends JController
{

    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }
    }

    public function indexAction()
    {
        $this->setResponseJSON(false);
        
        $this->view->setTemplate('GeneralContentLayout');
    }

    public function resourcesAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $vehiclesRepository = $em->getRepository('DefaultDb_Entities_Vehicle');
        $usersRepository = $em->getRepository('DefaultDb_Entities_User');
        $routesRepository = $em->getRepository('DefaultDb_Entities_Route');
        $controllerId = Model3_Auth::getCredentials('id');

        $controller = $usersRepository->find($controllerId);
        $vehicles = $vehiclesRepository->getByController($controller);
        $criteria = array(
            'controller' => $controller,
            'status' => DefaultDb_Entities_Route::STATUS_ACTIVE,
            'close' => DefaultDb_Entities_Route::CLOSE,
        );
        $routes = $routesRepository->findBy($criteria);
        $criteria = array(
            'parent' => $controller,
            'status' => DefaultDb_Entities_User::STATUS_ACTIVE
        );
        $drivers = $usersRepository->findBy($criteria);
        $this->view->drivers = $drivers;
        $this->view->vehicles = $vehicles;
        $this->view->routes = $routes;
        $this->view->whereAmI = array('Controlador de operaciones' => array('action' => 'index'));
        $this->view->setTemplate('Responsive-3.0');
        $this->view->title = 'Recursos';
        $this->view->description = 'Controlador de operaciones';
    }

    public function driversAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $usersRepository = $em->getRepository('DefaultDb_Entities_User');
        $controllerId = Model3_Auth::getCredentials('id');

        $controller = $usersRepository->find($controllerId);
        $criteria = array(
            'parent' => $controller,
            'status' => DefaultDb_Entities_User::STATUS_ACTIVE
        );
        $drivers = $usersRepository->findBy($criteria);
        $breadcrumb = array(
            'Controlador de operaciones' => array('action' => 'index'),
            'Recursos' => array('action' => 'resources'),
        );
        $this->view->drivers = $drivers;
        $this->view->whereAmI = $breadcrumb;
        $this->view->setTemplate('Responsive-3.0');
        $this->view->title = 'Conductores asignados';
        $this->view->description = 'Controlador de operaciones';
    }

    public function vehiclesAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $vehiclesRepository = $em->getRepository('DefaultDb_Entities_Vehicle');
        $usersRepository = $em->getRepository('DefaultDb_Entities_User');
        $controllerId = Model3_Auth::getCredentials('id');

        $controller = $usersRepository->find($controllerId);
        $vehicles = $vehiclesRepository->getByController($controller);
        $breadcrumb = array(
            'Controlador de operaciones' => array('action' => 'index'),
            'Recursos' => array('action' => 'resources'),
        );
        $this->view->getJsManager()->addJsVarEncode('urlEval', $this->view->url(array('action' => 'evalVehicle')));
        $this->view->vehicles = $vehicles;
        $this->view->whereAmI = $breadcrumb;
        $this->view->setTemplate('Responsive-3.0');
        $this->view->title = 'Vehículos asignados';
        $this->view->description = 'Controlador de operaciones';
    }

    public function assignVehicleAction()
    {
        $request = $this->getRequest();
        if ($request->isPost() === true)
        {
            $params = $request->getPost();
            $this->assign($params);
            $this->redirect('OperationController/Dashboard/vehicles');
            return;
        }
        $em = $this->getEntityManager('DefaultDb');
        $vehiclesRepository = $em->getRepository('DefaultDb_Entities_Vehicle');
        $usersRepository = $em->getRepository('DefaultDb_Entities_User');
        $vehicleId = $this->getRequest()->getParam('id');
        $controllerId = Model3_Auth::getCredentials('id');
        $controller = $usersRepository->find($controllerId);
        $criteria = array(
            'driver' => $controller,
            'status' => DefaultDb_Entities_Vehicle::STATUS_ACTIVE
        );
        $vehicles = $vehiclesRepository->findBy($criteria);
        $criteria = array(
            'status' => DefaultDb_Entities_Vehicle::STATUS_ACTIVE,
            'id' => $vehicleId,
        );
        $vehicle = $vehiclesRepository->findOneBy($criteria);
        $criteria = array(
            'parent' => $controller,
            'status' => DefaultDb_Entities_User::STATUS_ACTIVE
        );
        $drivers = $usersRepository->findBy($criteria);
        $breadcrumb = array(
            'Controlador de operaciones' => array('action' => 'index'),
            'Recursos' => array('action' => 'resources'),
            'Vehículos' => array('action' => 'vehicles'),
        );
        $this->view->drivers = $drivers;
        $this->view->vehicle = $vehicle;
        $this->view->vehicles = $vehicles;
        $this->view->whereAmI = $breadcrumb;
        $this->view->setTemplate('Responsive-3.0');
        $this->view->title = 'Asignar vehículo a conductor';
        $this->view->description = 'Controlador de operaciones';
    }

    private function assign(array $params)
    {
        $em = $this->getEntityManager('DefaultDb');
        $logVehicleDriverRepository = $em->getRepository('DefaultDb_Entities_LogVehicleDriver');
        $vehicleRepository = $em->getRepository('DefaultDb_Entities_Vehicle');
        $userRepository = $em->getRepository('DefaultDb_Entities_User');

        if (isset($params['driver']) === true && isset($params['vehicle']) === true)
        {
            $driverId = $params['driver'];
            $vehicleId = $params['vehicle'];
            $userSessionId = Model3_Auth::getCredentials('id');
            $user = $userRepository->find($userSessionId);
            $driver = $userRepository->find($driverId);
            $vehicle = $vehicleRepository->find($vehicleId);
            if ($user instanceof DefaultDb_Entities_User &&
                    $driver instanceof DefaultDb_Entities_User &&
                    $vehicle instanceof DefaultDb_Entities_Vehicle)
            {
                $vehicle->setDriver($driver);
                $logVehicleDriverRepository->addLog($vehicle, $driver, $user);
                $em->persist($vehicle);
                $em->flush();
            }
        }
    }

    public function routesAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $usersRepository = $em->getRepository('DefaultDb_Entities_User');
        $routesRepository = $em->getRepository('DefaultDb_Entities_Route');
        $controllerId = Model3_Auth::getCredentials('id');

        $controller = $usersRepository->find($controllerId);
        $criteria = array(
            'controller' => $controller,
            'status' => DefaultDb_Entities_Route::STATUS_ACTIVE,
            'close' => DefaultDb_Entities_Route::CLOSE,
        );
        $routes = $routesRepository->findBy($criteria);
        $breadcrumb = array(
            'Controlador de operaciones' => array('action' => 'index'),
            'Recursos' => array('action' => 'resources'),
        );
        $this->view->routes = $routes;
        $this->view->whereAmI = $breadcrumb;
        $this->view->setTemplate('Responsive-3.0');
        $this->view->title = 'Rutas asignados';
        $this->view->description = 'Controlador de operaciones';
    }

    public function evalUserAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $usersRepository = $em->getRepository('DefaultDb_Entities_User');
        $controllerId = Model3_Auth::getCredentials('id');

        $controller = $usersRepository->find($controllerId);
        $criteria = array(
            'parent' => $controller,
            'status' => DefaultDb_Entities_User::STATUS_ACTIVE
        );
        $drivers = $usersRepository->findBy($criteria);
        $criteria = array(
            'type' => DefaultDb_Entities_User::USER_STORER,
            'status' => DefaultDb_Entities_User::STATUS_ACTIVE
        );
        $storers = $usersRepository->findBy($criteria);
        $users = $drivers + $storers;
        $breadcrumb = array(
            'Controlador de operaciones' => array('action' => 'index'),
        );
        $this->view->users = $users;
        $this->view->whereAmI = $breadcrumb;
        $this->view->setTemplate('Responsive-3.0');
        $this->view->title = 'Evaluación de personal';
        $this->view->description = 'Controlador de operaciones';
    }

    public function evalVehicleAction()
    {
        $success = false;
        $request = $this->getRequest();
        if ($request->isPost() === true)
        {
            $params = $request->getPost($normalize);
            if (isset($params['type']) === true && isset($params['description']) === true)
            {
                $em = $this->getEntityManager('DefaultDb');
                $usersRepository = $em->getRepository('DefaultDb_Entities_User');
                $reportsVehiclesRepository = $em->getRepository('DefaultDb_Entities_ReportVehicle');
                $userId = Model3_Auth::getCredentials('id');
                $user = $usersRepository->find($userId);
                $type = $params['type'];
                $description = $params['description'];
                $reportsVehiclesRepository->insert($vehicle, $user, $description, $type);
                $success = true;
            }
        }
        $this->view->setUseTemplate(false);
        $this->view->success = $success;
    }

}