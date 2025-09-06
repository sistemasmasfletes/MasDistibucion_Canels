<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TipoMovimientoRepository")
 * @Table(name="tbltipomovimiento", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"chrTipoMovimiento"})})
 */
class DefaultDb_Entities_TipoMovimiento{
	 /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
     /**
     * @Column(type="string", name="chrTipoMovimiento", length=250, nullable=true)
     * @var string
     */
    protected $tipoMovimientos;
   
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $client;
    
    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    protected $timestamp;
    
    public function getId()
    {
        return $this->id;
    }
    
    
    public function getTipoMovimiento()
    {
        return $this->tipoMovimientos;
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

    public function setTipoMovimiento($tipoMovimientos)
    {
        $this->tipoMovimientos = $tipoMovimientos;
    }
    
    public function setClient($client) {
        $this->client = $client;
    }
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
  
}

