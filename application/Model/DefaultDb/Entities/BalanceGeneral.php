<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_BalanceGeneralRepository")
 * @Table(name="tblbalancegeneral")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_BalanceGeneral {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="date",  name="dtdFecha", nullable=true)
     * @var date
     */
    protected $fecha;
    
     /**
     * @Column(type="string",  name="strReferencia", nullable=true)
     * @var string
     */
    protected $referencia;

     /**
     * @Column(type="string",  name="strConcepto", nullable=true)
     * @var string
     */
    protected $concepto;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(name="intIdCliente", referencedColumnName="id", nullable=true)
     */
    protected $cliente;
    
    /**
     * @Column(type="string",  name="strEstatus", nullable=true)
     * @var string
     */
    protected $estatus;
    
    /**
     * @Column(type="float",  name="fltMonto", nullable=true)
     * @var float
     */
    protected $monto;
    
    /**
     * @Column(type="float",  name="fltCreditos", nullable=true)
     * @var float
     */
    protected $creditos;
    
    /**
     * @Column(type="float",  name="fltIngreso", nullable=true)
     * @var float
     */
    protected $ingresos;
    
    /**
     * @Column(type="float",  name="fltEgreso", nullable=true)
     * @var float
     */
    protected $egresos;
    
    /**
     * @Column(type="float",  name="fltBalance", nullable=true)
     * @var float
     */
    protected $balance;
    
    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    protected $timestamp;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Pagos")
     * @JoinColumn(name="intIdPagos", referencedColumnName="id", nullable=true)
     */
    protected $pagos;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TransferenciaCreditos")
     * @JoinColumn(name="intIdTransferenciaCreditos", referencedColumnName="id", nullable=true)
     */
    protected $transferenciaCreditos;
    
    
     /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TipoConcepto")
     * @JoinColumn(name="intIdTipoConcepto", referencedColumnName="id", nullable=true)
     */
    protected $tipoConcepto;

    
    /** @PrePersist */
    public function doStuffOnPrePersist() {
        $this->setTimestamp( new DateTime() );
    }

    public function getId() {
        return $this->id;
    }
    
    public function getFecha() {
        return $this->fecha;
    }

    public function getReferencia() {
        return $this->referencia;
    }
    
    public function getConcepto() {
        return $this->concepto;
    }
    
    public function getCliente() {
        return $this->cliente;
    }
    
    public function getEstatus() {
        return $this->estatus;
    }
    
    public function getMonto() {
        return $this->monto;
    }
    
    public function getCreditos() {
        return $this->creditos;
    }

    public function getIngresos() {
        return $this->ingresos;
    }

    public function getEgresos() {
        return $this->egresos;
    }
    
    public function getBalance() {
        return $this->balance;
    }
    
    public function getPagos() {
        return $this->pagos;
    }
    
    public function getTransferencia() {
        return $this->transferenciaCreditos;
    }
    
    public function getTipoConcepto() {
        return $this->tipoConcepto;
    }


    public function setId($id) {
        $this->id = $id;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha ;
    }

    public function setReferencia($referencia) {
        $this->referencia = $referencia;
    }
    
    public function setConcepto($concepto) {
        $this->concepto = $concepto;
    }
    
    public function setCliente($cliente) {
        $this->cliente = $cliente;
    }
    
    public function setEstatus($estatus) {
        $this->estatus = $estatus;
    }
    
    public function setMonto($monto) {
        $this->monto = $monto;
    }
    
    public function setCreditos($creditos) {
        $this->creditos = $creditos;
    }
   
    public function setIngresos($ingresos) {
        $this->ingresos = $ingresos;
    }
    
    public function setEgresos($egresos) {
        $this->egresos = $egresos;
    }
    
    public function setBalance($balance) {
        $this->balance = $balance;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
    
    public function setPagos($pagos) {
        $this->pagos = $pagos;
    }

    public function setTransferencia($transferencia) {
        $this->transferenciaCreditos = $transferencia;
    }
    
    public function setTipoConcepto($tipoConcepto) {
        $this->tipoConcepto = $tipoConcepto;
    }

}
