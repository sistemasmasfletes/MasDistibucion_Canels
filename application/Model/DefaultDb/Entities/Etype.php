<?php
/**
 * @Entity
 * @Table(name="etype")
 */
class DefaultDb_Entities_Etype{
    /**
    * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
     /**
     * @Column(type="string", length=30)
     * @var string
     */
    protected $name;
    
        
    public function setName($name){
        $this->name=$name;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getId(){
        return $this->id;
    }
}