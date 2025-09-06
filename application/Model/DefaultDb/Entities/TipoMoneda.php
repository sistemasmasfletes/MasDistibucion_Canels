<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TipoMonedaRepository")
 * @Table(name="tbltipomoneda", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"chrTipoMoneda"})})
 */
class DefaultDb_Entities_TipoMoneda{
    /**
     * @Id @GeneratedValue @Column(type="integer", name="intIDTipoMoneda")
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="string", name="chrTipoMoneda", length=250, nullable=true)
     * @var string
     */
    protected $TipoMoneda;
    
    /**
     * @Column(type="string", name="chrValor", length=250, nullable=true)
     * @var string
     */
    protected $Valor;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
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
    
    
    public function getId()
    {
        return $this->id;
    }
    
    
    public function getTipoMoneda()
    {
        return $this->TipoMoneda;
    }
    
    public function getValor()
    {
        return $this->Valor;
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
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTipoMoneda($TipoMoneda)
    {
        $this->TipoMoneda = $TipoMoneda;
    }
    
    public function setValor($Valor)
    {
        $this->Valor = $Valor;
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


