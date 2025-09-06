<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_BancosRepository")
 * @Table(name="tblbancos", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"chrName"})})
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_Bancos{
	 /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
     /**
     * @Column(type="string", name="chrName", length=250, nullable=true)
     * @var string
     */
    protected $name;
    
    /**
     * @Column(type="string", name="chrEstado", nullable=true)
     * @var string
     */
    private $estado;
    
     /**
     * @Column(type="string", name="chrClient", length=250, nullable=true)
     * @var string
     */
    protected $client;
    
    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    protected $timestamp;
    
        /** @PrePersist */
    public function doStuffOnPrePersist() {
        $this->setTimestamp( new DateTime() );
    }
    
    
    public function getId()
    {
        return $this->id;
    }
    
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getEstado() {
        return $this->estado;
    }
    
     public function getClient() {
        return $this->client;
    }
    
    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setEstado($estado) {
        $this->estado = $estado;
    }
    
    public function setClient($client) {
        $this->client = $client;
    }
    
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
  
}




