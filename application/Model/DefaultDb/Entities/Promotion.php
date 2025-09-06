<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_PromotionRepository")
 * @Table(name="promotion")
 */


class DefaultDb_Entities_Promotion
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="string", length=250, name="name")
     * @var string
     */
    protected $name;

    /**     
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $user;

     /**
     * @Column(type="integer", nullable=true)
     * @var integer
     */
    protected $numResources;


    public function getId()
    {
        return $this->id;
    }

     public function getName()
    {
        return $this->name;
    }

    public function getUser(){
        return $this->user;
    }

    public function getNumResources(){
        return $this->numResources;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setUser($user){
        $this->user = $user;
    }

    public function setNumResources($numResources){
        $this->numResources = $numResources;
    }
}