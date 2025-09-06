<?php
/**
 * Description of FavoriteUsersController
 *
 * @author drackor
 */
class Driver_FavoriteUsersController extends Model3_Controller
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
        $em = $this->getEntityManager('DefaultDb');

        $viewUser = new Model3_Session_Namespace('viewUser');
        $user = $em->find('DefaultDb_Entities_User', $viewUser->id);
        $this->view->user = $user;
        //Obtiene los favoritos del usuario cliente
        $favoriteUsersRepos = $em->getRepository('DefaultDb_Entities_FavoriteUsers');
        $favoriteUsers = $favoriteUsersRepos->findBy(array('client'=>$user, 'type' => 1));
        $this->view->favoriteUsersClient = $favoriteUsers;
        $favoriteUsersSuppliers = $favoriteUsersRepos->findBy(array('client'=>$user, 'type' => 2));
        $this->view->favoriteUsersSuppliers = $favoriteUsersSuppliers;
        
        if(Model3_Auth::getCredentials('type') == DefaultDb_Entities_User::USER_DRIVER)
        {
            //Se inserta como usuario visitado al log
            $logUserVisitedRepos = $em->getRepository('DefaultDb_Entities_LogUsersVisited');
            $driverId = Model3_Auth::getCredentials('id');
            $clientId = $viewUser->id;
            $logUserVisitedRepos->addLog($driverId, $clientId, DefaultDb_Entities_LogUsersVisited::ACTION_VISITED);
        }
    }
}