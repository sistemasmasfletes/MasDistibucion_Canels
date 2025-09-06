<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TipoMovimientosRepository")
 * @Table(name="tbltipomovimientos", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"chrTipoMovimiento"})})
 * @HasLifecycleCallbacks
 *  */
class DefaultDb_Entities_TipoMovimientos{
	 /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
     /**
     * @Column(type="string", name="chrTipoMovimiento", length=250, nullable=true)
     * @var string
     */
    protected $tipoMovimiento;
   
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
    
    
    public function getTipoMovimiento()
    {
        return $this->tipoMovimiento;
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

    public function setTipoMovimiento($tipoMovimiento)
    {
        $this->tipoMovimiento = $tipoMovimiento;
    }
    
    public function setClient($client) {
        $this->client = $client;
    }
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
  
}

