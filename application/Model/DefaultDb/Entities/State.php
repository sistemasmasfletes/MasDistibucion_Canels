<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_StateRepository")
 * @Table(name="states")
 */
class DefaultDb_Entities_State
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;
    
    /**
     * @Column(type="string", name="abbreviation", length=250, nullable=true)
     * @var string
     */
    protected $abbreviation;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Paises")
     * @JoinColumn(referencedColumnName="id", nullable=true)
     */
    protected $country;
    
     /**
     * @Column(type="string", name="chrEstatus", nullable=true)
     * @var string
    */
    private $estatus;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function getAbbreviation(){
        return $this->abbreviation;
    }
    
    public function getCountry(){
        return $this->country;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function setName($name){
        $this->name = $name;
    }
    
    public function setAbbreviation($abbreviation){
        $this->abbreviation = $abbreviation;
    }
    
    public function setCountry($country){
        $this->country = $country;
    }
    public function getEstatus() {
        return $this->estatus;
    }
    public function setEstatus($estatus) {
        $this->estatus = $estatus;
    }
}