<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_CiudadesRepository")
 * @Table(name="city")
 */

class DefaultDb_Entities_City{
    
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
     * @ManyToOne(targetEntity="DefaultDb_Entities_State")
     * @JoinColumn(nullable=true)
     */
    protected $state;
    
     /**
     * @Column(type="string", name="chrEstatus", nullable=true)
     * @var string
    */
    private $estatus;

    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getState(){
        return $this->state;
    }

    public function setId($id){
        $this->id = $id;
    }
    
    public function setName($name){
        $this->name = $name;
    }
    
    public function setStatus($state){
        $this->state = $state;
    }
    
    public function getEstatus() {
        return $this->estatus;
    }
    
    public function setEstatus($estatus) {
        $this->estatus = $estatus;
    }
    
    
}
?>
