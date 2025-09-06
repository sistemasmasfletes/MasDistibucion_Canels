<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_CuentasRepository")
 * @Table(name="tblcuentas", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"chrNumeroCuenta"})})
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_Cuentas{

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="string",  name="chrNumeroCuenta", nullable=true)
     * @var string
     */
    protected $numeroCuenta;
    
    /**
     * @Column(type="string",  name="chrCuenta",nullable=true)
     * @var string
     */
    protected $cuenta;
    
    /**
     * @Column(type="string",  name="chrClabeInterbancaria",nullable=true)
     * @var string
     */
    protected $clabeInterbancaria;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TipoMonedas")
     * @JoinColumn(name="intIDTipoMoneda", referencedColumnName="id")
     */
    protected $moneda;
    
    
     /**
     * @Column(type="string",  name="chrTipoOperador",nullable=true)
     * @var string
     */
    protected $tipoOperador;
            
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Paises")
     * @JoinColumn(name="intIDPais", referencedColumnName="id")
     */
    protected $pais;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Bancos")
     * @JoinColumn(name="intIDBanco", referencedColumnName="id")
     */
    private $banco;
    
    
    /**
     * @Column(type="string", name="chrEstado", nullable=true)
     * @var string
     */
    private $estado;

     /**
     * @Column(type="string", name="chrCliente", length=250, nullable=true)
     * @var string
     */
    protected $cliente;

    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    protected $timestamp;
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TipoPagos")
     * @JoinColumn(name="intIDTipoPago", referencedColumnName="id")
     */
    protected $tipoPago;

    
    /** @PrePersist */
    public function doStuffOnPrePersist() {
        $this->setTimestamp( new DateTime() );
    }
    
    
    public function getId() {
        return $this->id;
    }
    
    public function getNumeroCuenta() {
        return $this->numeroCuenta;
    }
    
    public function getCuenta() {
        return $this->cuenta;
    }
    
    public function getClabeInterbancaria() {
        return $this->clabeInterbancaria;
    }
    
    public function getMoneda() {
        return $this->moneda;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getTipoOperador() {
        return $this->tipoOperador;
    }

    public function getPais() {
        return $this->pais;
    }
    
    public function getEstado() {
        return $this->estado;
    }
    
    public function getCliente() {
        return $this->cliente;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function getBanco() {
        return $this->banco;
    }
    
    public function setId($id) {
     $this->id = $id;
    }
    
    public function setNumeroCuenta($numeroCuenta) {
        $this->numeroCuenta = $numeroCuenta;
    }
    
     public function setCuenta($cuenta) {
       $this->cuenta=$cuenta;
    }
    
    public function setClabeInterbancaria($clabeInterbancaria) {
       $this->clabeInterbancaria=$clabeInterbancaria;
    }
    
    public function setMoneda($moneda) {
        $this->moneda = $moneda;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function setTipoOperador($tipoOperador) {
        $this->tipoOperador = $tipoOperador;
    }

    public function setPais($pais) {
        $this->pais = $pais;
    }
    
    public function setEstado($estado) {
        $this->estado = $estado;
    }
    
    public function setCliente( $cliente ) {
        $this->cliente = $cliente;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
    
    public function setBanco( $banco ) {
        $this->banco = $banco;
    }
       public function setTipoPago($tipoPago) {
        $this->tipoPago = $tipoPago;
    }
    public function getTipoPago() {
        return $this->tipoPago;
    }

}
