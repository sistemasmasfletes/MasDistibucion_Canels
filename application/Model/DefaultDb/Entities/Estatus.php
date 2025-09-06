<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_EstatusRepository")
 * @Table(name="tblestatus", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"chrEstatu"})})
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_Estatus{
	 /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="string", name="chrEstatu", length=250, nullable=true)
     * @var string
     */
    protected $estatus;
    
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
    
    
    public function getEstatus()
    {
        return $this->estatus;
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

    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    }
    
    public function setClient($client) {
        $this->client = $client;
    }
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
  
}



