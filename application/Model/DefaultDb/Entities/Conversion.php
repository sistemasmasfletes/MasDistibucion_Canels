<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ConversionRepository")
 * @Table(name="tblconversion")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_Conversion {
    
   
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TipoMonedas")
     * @JoinColumn(name="intIDMoneda", referencedColumnName="id")
     */
    protected $moneda;
    
    /**
     * @Column(type="string",  name="chrCompra", nullable=true)
     * @var string
     */
    protected $compra;
    
    /**
     * @Column(type="string",  name="chrVenta", nullable=true)
     * @var string
     */
    protected $venta;

    /**
     * @Column(type="datetime",  name="dtdFecha", length=10, nullable=true)
     * @var string
     */
    protected $fecha;
    
    /**
     * @Column(type="string",  name="chrCreditos", nullable=true)
     * @var string
     */
    private $creditos;

    /**
     * @Column(type="string", name="chrClient", length=250, nullable=true)
     * @var string
     */
    protected $client;

     /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    private $timestamp;
     
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
    
    public function getCompra() {
        return $this->compra;
    }

    public function getVenta() {
        return $this->venta;
    }

    public function getFecha() {
        return $this->fecha;
    }
    
    public function getCreditos() {
        return $this->creditos;
    }

    public function getClient() {
        return $this->client;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function setId($id) {
        $this->id = $id;
    }
    
     public function setMoneda($moneda) {
        $this->moneda = $moneda;
    }
    
    public function setCompra($compra) {
        $this->compra = $compra;
    }

    public function setVenta($venta) {
        $this->venta = $venta;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }
    
    public function setCreditos($creditos) {
        $this->creditos = $creditos;
    }
    
    public function setClient($client) {
        $this->client = $client;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

}
