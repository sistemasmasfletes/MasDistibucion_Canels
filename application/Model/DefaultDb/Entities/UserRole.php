<?php
/**
 * @Entity
 * @Table(name="user_role")
 */
class DefaultDb_Entities_UserRole{
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
     * @ManyToOne(targetEntity="DefaultDb_Entities_Role")
     */
    protected $role;

    public function setUser($user){
        $this->$user=$user;
    }

    public function getUser(){
        return $this->$user;
    }

    public function setRole($role){
        $this->$role=$role;
    }

    public function getRole(){
        return $this->$role;
    }

    public function getId(){
        return $this->$id;
    }
    
}