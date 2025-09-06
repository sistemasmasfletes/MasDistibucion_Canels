<?php
/**
 * @Entity
 * @Table(name="role_action")
 */
class DefaultDb_Entities_RoleAction{
    /**
    * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Role")
     */
    protected $role;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_ElementAction")
     */
    protected $eaction;

    public function setRole($role){
        $this->role=$role;
    }

    public function getRole(){
        return $this->role;
    } 

    public function setEaction($action){
        $this->eaction=$action;
    }

    public function getEaction(){
        return $this->eaction;
    }

    public function getId(){
        return $this->id;
    }
    
}