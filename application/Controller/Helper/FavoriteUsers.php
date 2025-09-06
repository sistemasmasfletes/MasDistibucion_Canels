<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FavoriteUsers
 *
 * @author drackor
 */
class Helper_FavoriteUsers
{
    private $_em;

    public function __construct($em)
    {
        $this->_em = $em;
    }

    //public function addFavorite($clientId, $favoriteClientId, $typeFavorite ,$productoId, $orderId )
    public function addFavorite($clientId, $favoriteClientId, $typeFavorite ,$productoId = NULL )
    {
        $res = false;
        $client = $this->_em->find('DefaultDb_Entities_User', $clientId);
        $favoriteClient = $this->_em->find('DefaultDb_Entities_User', $favoriteClientId);
       
        $favoriteUserRepos = $this->_em->getRepository('DefaultDb_Entities_FavoriteUsers');
        
        //$order = $this->_em->find('DefaultDb_Entities_M3CommerceOrder',$orderId);
        //$favoriteUser = $favoriteUserRepos->findBy(array('client'=>$client, 'favoriteClient'=>$favoriteClient,'type' => $typeFavorite,'order'=>$order,'producto'=>$producto));
        if ($productoId == NULL){
         $favoriteUser = $favoriteUserRepos->findBy(array('client'=>$client, 'favoriteClient'=>$favoriteClient,'type' => $typeFavorite,'producto'=>null));   
        }else{
         $producto = $this->_em->find('DefaultDb_Entities_Product',$productoId);
         $favoriteUser = $favoriteUserRepos->findBy(array('client'=>$client, 'favoriteClient'=>$favoriteClient,'type' => $typeFavorite,'producto'=>$producto));   
        }
        

        if(is_array($favoriteUser) && count($favoriteUser) == 0)
        {
            $favoriteUser = new DefaultDb_Entities_FavoriteUsers();
            $favoriteUser->setClient($client);
            $favoriteUser->setFavoriteClient($favoriteClient);
            $favoriteUser->setType($typeFavorite);
            if ($productoId != NULL){
                $favoriteUser->setproducto($producto);
            }else{
                $favoriteUser->setproducto(null);
            }
            //$favoriteUser->setOrder($order);

            $this->_em->persist($favoriteUser);
            $this->_em->flush();
            $res = true;
        }
        return $res;
    }

    public function removeFavorite($clientId, $favoriteClientId, $typeFavorite)
    {
        $res = false;
        $client = $this->_em->find('DefaultDb_Entities_User', $clientId);
        $favoriteClient = $this->_em->find('DefaultDb_Entities_User', $favoriteClientId);

        $favoriteUserRepos = $this->_em->getRepository('DefaultDb_Entities_FavoriteUsers');

        $favoriteUser = $favoriteUserRepos->findBy(array('client'=>$client, 'favoriteClient'=>$favoriteClient, 'type' => $typeFavorite));
        if(is_array($favoriteUser) && count($favoriteUser) > 0)
        {
            $this->_em->remove($favoriteUser[0]);

            $this->_em->flush();
            $res = true;
        }
        return $res;
    }

    public function removeFavoriteById($favoriteId)
    {
        $res = false;
        $favoriteUser = $this->_em->find('DefaultDb_Entities_FavoriteUsers', $favoriteId);
        if($favoriteUser instanceof DefaultDb_Entities_FavoriteUsers)
        {
            $this->_em->remove($favoriteUser);

            $this->_em->flush();
            $res = true;
        }
        return $res;
    }
}
