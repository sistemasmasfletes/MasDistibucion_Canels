<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_AdministracionLogClienteRepository")
 * @Table(name="tbladministracionlogcliente")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_AdministracionLogCliente{

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @IdConcepto @Column(type="integer", name="intIdConcepto")
     * @var integer  
     * */
    protected $IdConcepto;
    
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TransferenciaCreditos")
     * @JoinColumn(name="intIdTransferencia", referencedColumnName="id", nullable=true)
     */
    protected $transferencia;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_CompraCreditos")
     * @JoinColumn(name="intIdCompraCreditos", referencedColumnName="id", nullable=true)
     */
    protected $compraCreditos;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TipoConcepto")
     * @JoinColumn(name="intIdTipoConcepto", referencedColumnName="id", nullable=true)
     */
    protected $tipoConcepto;
   
    /**
     * @concepto @Column(type="string", name="strConcepto")
     * @var String  
     * */
    protected $concepto;
    
    /**
     * @Column(type="date",  name="dtdFecha", nullable=true)
     * @var date
     */
    protected $fecha;
    
    /**
     * @concepto @Column(type="string", name="chrReferencia", nullable=true)
     * @var String  
     * */
    protected $referencia;
    
    /**
     * @concepto @Column(type="string", name="chrBanco", nullable=true)
     * @var String  
     * */
    protected $banco;
    
    /**
     * @concepto @Column(type="string", name="chrTipoPago", nullable=true)
     * @var String  
     * */
    protected $tipoPago;

    /**
     * @monto @Column(type="float", name="numMonto", nullable=true)
     * @var float  
     * */
    protected $monto;
    
    /**
     * @monto @Column(type="float", name="numCreditos", nullable=true)
     * @var float  
     * */
    protected $creditos;
    
    /**
     * @monto @Column(type="float", name="fltSaldo")
     * @var float  
     * */
    protected $saldo;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
    */
    protected $cliente;
    
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

    public function getIdConcepto() {
        return $this->IdConcepto;
    }
    
    public function getTransferencia() {
        return $this->transferencia;
    }
    
    public function getCompraCreditos() {
        return $this->compraCreditos;
    }
    
    public function getTipoConcepto() {
        return $this->tipoConcepto;
    }

    public function getConcepto() {
        return $this->concepto;
    }
    
    public function getFecha() {
        return $this->fecha;
    }
    
    public function getReferencia() {
        return $this->referencia;
    }
    
    public function getBanco() {
        return $this->banco;
    }

    public function getTipoPago() {
        return $this->tipoPago;
    }
    
    public function getMonto() {
        return $this->monto;
    }
    
    public function getCreditos() {
        return $this->creditos;
    }

    public function getSaldo() {
        return $this->saldo;
    }
    
    public function getCliente() {
        return $this->cliente;
    }
    
    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function setId($id) {
        $this->id = $id;
    }

    public function setIdConcepto($idConcepto) {
        $this->IdConcepto = $idConcepto;
    }
    
    public function setTransferencia($transferencia) {
        $this->transferencia = $transferencia;
    }
    
    public function setCompraCreditos($compraCreditos) {
        $this->compraCreditos = $compraCreditos;
    }
    
    public function setTipoConcepto($tipoConcepto) {
        $this->tipoConcepto = $tipoConcepto;
    }
    
    public function setConcepto($concepto) {
        $this->concepto = $concepto;
    }
    
    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }
    
    public function setReferencia($referencia) {
        $this->referencia = $referencia;
    }

    public function setBanco($banco) {
        $this->banco = $banco;
    }
    
    public function setTipoPago($tipoPago) {
        $this->tipoPago = $tipoPago;
    }
    
    public function setMonto($monto) {
        $this->monto = $monto;
    }

    public function setCreditos($creditos) {
        $this->creditos = $creditos;
    }
    
    public function setSaldo($saldo) {
        $this->saldo = $saldo;
    }
    public function setCliente($cliente) {
       $this->cliente = $cliente;
    }
    
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

}
