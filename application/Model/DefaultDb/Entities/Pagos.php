<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_PagosRepository")
 * @Table(name="tblpagos")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_Pagos {

    const TIPO_COMPRA_VENTA = 1; 
    const TIPO_COMPRA_CREDITOS = 2;
    const TIPO_FLETES = 3;
    
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(name="intIdUser", referencedColumnName="id")
     */
    protected $usuario;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(name="intIdCliente", referencedColumnName="id", nullable=true)
     */
    protected $cliente;
    
     /**
     * @Column(type="integer",  name="strOrden", nullable=true)
     * @var integer
     */
    protected $orden;
    
    /**
     * @Column(type="float",  name="numMontoCompra", nullable=true)
     * @var float
     */
    protected $montoCompra;
    
    /**
     * @Column(type="float",  name="numMontoCreditos", nullable=true)
     * @var float
     */
    protected $montoCreditos;

    /**
     * @Column(type="date",  name="dtdFecha", nullable=true)
     * @var date
     */
    protected $fecha;

    /**
     * @Column(type="integer",  name="numEstatus", nullable=true)
     * @var integer
     */
    private $estatus;
    
    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    protected $timestamp;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_M3CommerceOrder")
     * @JoinColumn(name="intIdCompraVenta", referencedColumnName="id", nullable=true)
     */
    protected $compraVenta;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_CompraCreditos")
     * @JoinColumn(name="intIdCompraCreditos", referencedColumnName="id", nullable=true)
     */
    protected $compraCreditos;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_PromotionSchedule")
     * @JoinColumn(name="intIdPromocion", referencedColumnName="id", nullable=true)
     */
    protected $promocion;
    
     /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TipoConcepto")
     * @JoinColumn(name="intIdTipoConcepto", referencedColumnName="id", nullable=true)
     */
    protected $tipoConcepto;
    
     /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TipoDebito")
     * @JoinColumn(name="intIdTipoDebito", referencedColumnName="id", nullable=true)
     */
    protected $tipoDebito;
    
     /**
     * @Column(type="string",  name="chrDescripcion", nullable=true)
     * @var string
     */
    protected $descripcion;
    
    /** @PrePersist */
    public function doStuffOnPrePersist() {
        $this->setTimestamp( new DateTime() );
    }
    
    public function getId() {
        return $this->id;
    }

    public function getUsuario() {
        return $this->usuario;
    }
    
    public function getCliente() {
        return $this->cliente;
    }
    
     public function getOrden() {
        return $this->orden;
    }
    
    public function getMontoCompra() {
        return $this->montoCompra;
    }
    
    public function getMontoCreditos() {
        return $this->montoCreditos;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function getEstatus() {
        return $this->estatus;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function getCompraVenta() {
        return $this->compraVenta;
    }
    
    public function getCompraCreditos() {
        return $this->compraCreditos;
    }

    public function getPromocion() {
        return $this->promocion;
    }

    public function getTipoConcepto() {
        return $this->tipoConcepto;
    }
    
    public function getTipoDebito() {
        return $this->tipoDebito;
    }
    
    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function setCliente($cliente) {
        $this->cliente = $cliente;
    }
    
    public function setOrden($orden) {
        $this->orden = $orden;
    }
    
    public function setMontoCompra($montoCompra) {
        $this->montoCompra = $montoCompra;
    }
    
    public function setMontoCreditos($montoCreditos) {
        $this->montoCreditos = $montoCreditos;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function setEstatus($estatus) {
        $this->estatus = $estatus;
    }
    
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
    
    public function setCompraVenta($compraVenta) {
        $this->compraVenta = $compraVenta;
    }
    
    public function setCompraCreditos($compraCreditos) {
        $this->compraCreditos = $compraCreditos;
    }
    
    public function setPromocion($promocion) {
        $this->promocion = $promocion;
    }
    
    public function setTipoConcepto($tipoConcepto) {
        $this->tipoConcepto = $tipoConcepto;
    }
    
    public function setTipoDebito($tipoDebito) {
        $this->tipoDebito = $tipoDebito;
    }
    
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

}
