<?php
/**
 * @Entity
 * @Table(name="role")
 */
class DefaultDb_Entities_Role{
    /**
    * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="string", length=50)
     * @var string
     */
    protected $name;

    /**
     * @Column(type="string", length=150, nullable=TRUE)
     * @var string
     */
    protected $description;

    public function setName($name){
        $this->name=$name;
    }

    public function getName(){
        return $this->name;
    }

    public function setDescription($description){
        $this->description=$description;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id=$id;
    }
}