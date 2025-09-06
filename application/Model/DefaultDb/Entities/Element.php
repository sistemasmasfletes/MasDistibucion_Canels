<?php
/**
 * @Entity
 * @Table(name="elements")
 */
class DefaultDb_Entities_Element{
    /**
    * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
     /**
     * @Column(type="string", length=70)
     * @var string
     */
    protected $name;

    /**
     * @Column(type="string", length=80)
     * @var string
     */
    protected $title;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Etype")
     */
    protected $type;
    
    public function setName($name){
        $this->name=$name;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function setTitle($title){
        $this->title=$title;
    }
    
    public function getTitle(){
        return $this->title;
    }

    public function setType($type){
        $this->type=$type;
    }
    
    public function getType(){
        return $this->type;
    }
    
    public function getId(){
        return $this->id;
    }
}