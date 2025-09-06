<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TerminalBancariaRepository")
 * @Table(name="tblterminalbancariapagos")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_TerminalBancaria {

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
    protected $montoTerminal;
    
    
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
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(name="intIdUsuario", referencedColumnName="id")
     */
    protected $usuario;
    
    
    
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
        return $this->montoTerminal;
    }

    public function getIdTransferencia() {
        return $this->idTransferencia;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function getUsuario() {
        return $this->usuario;
    }
    
    
    public function setId($id) {
        $this->id = $id;
    }

     public function setPagos($pagos) {
        $this->pagos = $pagos;
    }

    public function setMonto($monto) {
        $this->montoTerminal = $monto;
    }

    public function setIdTransferencia($idTransferencia) {
        $this->idTransferencia = $idTransferencia;
    }
    
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }
    
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

}
