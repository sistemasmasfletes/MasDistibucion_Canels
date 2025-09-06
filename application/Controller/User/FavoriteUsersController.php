<?php

class User_FavoriteUsersController extends Model3_Controller
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
        $credentials = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        
        $this->view->getJsManager()->addJs('view/helper/element/product.js');
        $this->view->getJsManager()->addJsVar('urlAddToCart', '"' . $this->view->url(array('module' => 'User', 'controller' => 'AjaxCart', 'action' => 'addToCart')) . '"');

        $user1 = $em->find('DefaultDb_Entities_User', $credentials['id']);
        $favoriteUserRepos = $em->getRepository('DefaultDb_Entities_FavoriteUsers');
        $favoriteUsersBuyers = $favoriteUserRepos->findBy(array('client' => $user1, 'type' => DefaultDb_Entities_FavoriteUsers::FAVORITE_BUYER));
        //$favoriteUsersSellers = $favoriteUserRepos->findBy(array('client' => $user1, 'type' => DefaultDb_Entities_FavoriteUsers::FAVORITE_SELLER));
        $favoriteUsersSellers = $favoriteUserRepos->createQueryBuilder('l')
        ->where("l.client = :client")
        ->andWhere("l.type = 2")
        ->setParameter('client',$user1)
        ->groupBy("l.favoriteClient")
        ->getQuery()
        ->getResult();
        
        $this->view->favoriteUsersBuyers = $favoriteUsersBuyers;
        $this->view->favoriteUsersSellers = $favoriteUsersSellers;
        $this->view->_em = $em;
    }
    
    public function deleteAction()
    {
        $favoriteId = $this->getRequest()->getParam('id');
        $res = false;
        if($favoriteId)
        {
            $helperFavorites = new Helper_FavoriteUsers($this->getEntityManager('DefaultDb'));
            $res = $helperFavorites->removeFavoriteById($favoriteId);
        }
        $this->view->res = $res;
    }
}