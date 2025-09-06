<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_CompraCreditosRepository")
 * @Table(name="tblcompracreditos")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_CompraCreditos {

    const STATUS_PAGADO = 1;
    const STATUS_PENDIENTE = 0;

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="string",  name="chrUsuario", nullable=true)
     * @var string
     */
    protected $usuario;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(name="intIDUser", referencedColumnName="id")
     */
    protected $cliente;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TipoPagos")
     * @JoinColumn(name="intIDTipoPago", referencedColumnName="id")
     */
    protected $tipoPago;

    /**
     * @Column(type="float",  name="numMontoCompra", nullable=true)
     * @var float
     */
    protected $montoCompra;

    /**
     * @Column(type="float",  name="numCreditos", nullable=true)
     * @var float
     */
    protected $creditos;

    /**
     * @Column(type="date",  name="dtdFecha", nullable=true)
     * @var date
     */
    protected $fecha;

    /**
     * @Column(type="string",  name="chrPath", nullable=true)
     * @var string
     */
    private $path;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Cuentas")
     * @JoinColumn(name="intIDCuenta", referencedColumnName="id")
     */
    protected $cuenta;

    /**
     * @Column(type="string",  name="chrReferencia", nullable=true)
     * @var string
     */
    protected $referencia;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Estatus")
     * @JoinColumn(name="intIDEstatus", referencedColumnName="id")
     */
    private $estatus;
    
    /**
     * @Column(type="string",  name="chrComentario", nullable=true)
     * @var string
     */
    protected $comentario;
    
    
    /** @PrePersist */
    public function doStuffOnPrePersist() {
        $this->setTimestamp( new DateTime() );
    }

    

    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    protected $timestamp;

    public function getId() {
        return $this->id;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getTipoPago() {
        return $this->tipoPago;
    }

    public function getMontoCompra() {
        return $this->montoCompra;
    }

    public function getCreditos() {
        return $this->creditos;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function getPath() {
        return $this->path;
    }

    public function getReferencia() {
        return $this->referencia;
    }

    public function getCuenta() {
        return $this->cuenta;
    }

    public function getEstatus() {
        return $this->estatus;
    }

    public function getCliente() {
        return $this->cliente;
    }


    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function getComentario() {
        return $this->comentario;
    }


    public function setId($id) {
        $this->id = $id;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function setTipoPago($tipoPago) {
        $this->tipoPago = $tipoPago;
    }

    public function setMontoCompra($montoCompra) {
        $this->montoCompra = $montoCompra;
    }

    public function setCreditos($creditos) {
        $this->creditos = $creditos;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function setReferencia($referencia) {
        $this->referencia = $referencia;
    }

    public function setCuenta($cuenta) {
        $this->cuenta = $cuenta;
    }

    public function setEstatus($estatus) {
        $this->estatus = $estatus;
    }

    public function setCliente($cliente) {
        $this->cliente = $cliente;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
    
    public function setComentario($comentario) {
        $this->comentario = $comentario;
    }

}
