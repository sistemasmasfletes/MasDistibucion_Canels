<?php

class Ajax_FavoriteController extends Model3_Controller
{

    public function init()
    {
        $this->view->setUseTemplate(false);
    }

    public function addFavoriteAction()
    {
        $res = false;
        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $helperFavorites = new Helper_FavoriteUsers($this->getEntityManager('DefaultDb'));
            
            $favoriteClientId = $post['clientId'];
            $typeFavorite = $post['type'];
            $clientId = Model3_Auth::getCredentials('id');
            
            $res = $helperFavorites->addFavorite($clientId, $favoriteClientId,$typeFavorite);
        }
        $this->view->res = json_encode($res);
    }

    public function removeFavoriteAction()
    {
        $res = false;
        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $helperFavorites = new Helper_FavoriteUsers($this->getEntityManager('DefaultDb'));
            
            $favoriteClientId = $post['clientId'];
            $typeFavorite = $post['type'];
            $clientId = Model3_Auth::getCredentials('id');
            
            $res = $helperFavorites->removeFavorite($clientId, $favoriteClientId,$typeFavorite);
        }
        $this->view->res = json_encode($res);
    }
}