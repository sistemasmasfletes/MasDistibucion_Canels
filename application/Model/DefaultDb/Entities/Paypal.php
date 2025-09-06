<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_PaypalRepository")
 * @Table(name="tblpaypalpagos")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_Paypal {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Pagos")
     * @JoinColumn(name="intIdPagos", referencedColumnName="id", nullable=true)
     */
    protected $pagos;

        
    /**
     * @Column(type="float",  name="numMonto", nullable=true)
     * @var float
     */
    protected $montoPaypal;
    
     /**
     * @Column(type="string",  name="chrCurrency", nullable=true)
     * @var string
     */
    protected $currency;
    
     /**
     * @Column(type="string",  name="chrIdTransferencia", nullable=true)
     * @var string
     */
    protected $idTransferencia;
    
    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    
    protected $timestamp;
    
    
    
    /** @PrePersist */
    public function doStuffOnPrePersist() {
        $this->setTimestamp( new DateTime() );
    }
    
    public function getId() {
        return $this->id;
    }
    
     public function getPagos() {
        return $this->pagos;
    }

    public function getMonto() {
        return $this->montoPaypal;
    }
    
    public function getCurrency() {
        return $this->currency;
    }

    public function getIdTransferencia() {
        return $this->idTransferencia;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }
    
    
    public function setId($id) {
        $this->id = $id;
    }

     public function setPagos($pagos) {
        $this->pagos = $pagos;
    }

    public function setMonto($monto) {
        $this->montoPaypal = $monto;
    }
    
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    public function setIdTransferencia($idTransferencia) {
        $this->idTransferencia = $idTransferencia;
    }
    
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

}
