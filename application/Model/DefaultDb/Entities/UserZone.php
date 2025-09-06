<?php
/**
 * @Entity
 * @Table(name="user_zone")
 */
class DefaultDb_Entities_UserZone{
    /**
    * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $user;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Zone")
     */
    protected $zone;

    public function setUser($user){
        $this->user=$user;
    }

    public function getUser(){
        return $this->user;
    }

    public function setZone($zone){
        $this->zone=$zone;
    }

    public function getZone(){
        return $this->zone;
    }

    public function getId(){
        return $this->id;
    }
    
}