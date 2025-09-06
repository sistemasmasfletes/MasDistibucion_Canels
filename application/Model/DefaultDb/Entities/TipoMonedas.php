<?php
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TipoMonedasRepository")
 * @Table(name="tbltipomonedas", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"chrMoneda"})})
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_TipoMonedas{
	 /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
     /**
     * @Column(type="string", name="chrMoneda", length=250, nullable=true)
     * @var string
     */
    protected $moneda;
    
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
    
     /**
     * @Column(type="string", name="chrCurrencyCode", length=20, nullable=true)
     * @var string
     */
    protected $currencyCode;
    
    
     /** @PrePersist */
    public function doStuffOnPrePersist() {
        $this->setTimestamp( new DateTime() );
    }
    
    public function getId() {
        return $this->id;
    }
    
    
    public function getMoneda() {
        return $this->moneda;
    }
    
    public function getClient() {
        return $this->client;
    }
    
    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function getCurrencyCode() {
        return $this->currencyCode;
    }
    
    
    public function setId($id) {
        $this->id = $id;
    }

    public function setMoneda($moneda) {
        $this->moneda = $moneda;
    }
    
    public function setClient($client) {
        $this->client = $client;
    }
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
    
    public function setCurrencyCode($currencyCode) {
        $this->currencyCode = $currencyCode;
    }
  
}

