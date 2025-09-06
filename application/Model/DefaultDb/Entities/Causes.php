<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_CausesRepository")
 * @Table(name="causes")
 */
class DefaultDb_Entities_Causes
{
    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;
     /**
     * @Column(type="string", name="name")
     * @var string
     */
    protected $name;
     /**
     * @Column(type="string", name="description")
     * @var string
     */
    protected $description;
    
    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getDescription(){
        return $this->description;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function setName($name){
        $this->name = $name;
    }
    
    public  function setDescription($description){
        $this->description = $description;
    }
}