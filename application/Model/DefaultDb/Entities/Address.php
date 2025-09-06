<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_AddressRepository")
 * @Table(name="address")
 */

Class DefaultDb_Entities_Address{
    
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="string", name="address", length=255, nullable=true)
     * @var string
     */
    protected $address;

    /**
     * @Column(type="string", name="neighborhood", length=255, nullable=true)
     * @var string
     */
    protected $neighborhood;
    

    
    /**
     * @Column(type="integer", name="zipcode", nullable=true)
     * @var integer
     */
    protected $zipcode;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_City")
     * @JoinColumn(nullable=true)
     */
    protected $city;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_State")
     * @JoinColumn(nullable=true)
     */
    protected $state;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Paises")
     * @JoinColumn(nullable=true)
     */
    protected $country;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(nullable=true)
     */
    protected $user;
    
     /**
     * @Column(type="integer")
     * @var integer
     */
    protected $authorized;
    
    /**
     * @Column(type="integer", name="zone_id")
     * @var integer
     */
    protected $zoneId;
    
    public function getId(){
        return $this->id;
    }
    
    public function getCountry(){
        return $this->country;
    }
    
    public function getState(){
        return $this->state;
    }
    
    public function getCity(){
        return $this->city;
    }
    
    public function getAddress(){
        return $this->address;
    }

    public function getNeighborhood(){
        return $this->neighborhood;
    }
    
    public function getZipcode(){
        return $this->zipcode;
    }
    
    public function getUser(){
        return $this->user;
    }
    
    public function getAuthorized(){
        return $this->authorized;
    }
    
    public function getZoneId(){
        return $this->zoneId;
    }


    public function setUser($user){
        $this->user = $user;
    }

    public function setId($id){
        $this->id = $id;
    }
    
    public function setCountry($country){
        $this->country = $country;
    }
    
    public function setState($state){
        $this->state = $state;
    }
    
    public function setCity($city){
        $this->city = $city;
    }
    
    public function setAddress($address){
        $this->address = $address;
    }

    public function setNeighborhood($neighborhood){
        $this->neighborhood = $neighborhood;
    }
    
    public function setZipcode($zipcode){
        $this->zipcode = $zipcode;
    }
    
    public function setAuthorized($authorized){
        $this->authorized = $authorized;
    }
    
    public function setZoneId($zone){
        $this->zoneId = $zone;
    }
}