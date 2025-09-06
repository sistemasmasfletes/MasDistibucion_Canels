<?php

/**
 * @Entity
 * @Table(name="favorite_users")
 */
class DefaultDb_Entities_FavoriteUsers
{
    const FAVORITE_BUYER = 1;
    const FAVORITE_SELLER = 2;
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $client;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $favoriteClient;
    /**
     * @Column(type="integer", name="type")
     * @var integer
     */
    protected $type;
     /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Product")
     */
    protected $producto;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_M3CommerceOrder")
     */
    protected $order;
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getClient()
    {
        return $this->client;
    }

    public function getType()
    {
        return $this->type;
    }
    
    public function setClient($client)
    {
        $this->client = $client;
    }

    public function getFavoriteClient()
    {
        return $this->favoriteClient;
    }

    public function setFavoriteClient($favoriteClient)
    {
        $this->favoriteClient = $favoriteClient;
    }
    
    public function setType($type)
    {
        $this->type = $type;
    }
    
    public function getProducto()
    {
        return $this->producto;
    }

    public function setproducto($producto)
    {
        $this->producto = $producto;
    }
    
    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }
    
}
