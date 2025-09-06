<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TransferenciaCreditosRepository")
 * @Table(name="tbltransferenciacreditos")
 * @HasLifecycleCallbacks
 */
class DefaultDb_Entities_TransferenciaCreditos {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(name="intIDUsuario", referencedColumnName="id")
     */
    protected $usuario;

    /**
     * @Column(type="date",  name="dtdFecha", nullable=true)
     * @var date
     */
    protected $fecha;

    /**
     * @Column(type="float",  name="numCreditos", nullable=true)
     * @var string
     */
    protected $creditos;

    /**
     * @Column(type="float",  name="numMonto", nullable=true)
     * @var float
     */
    protected $monto;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Category")
     * @JoinColumn(name="intIDCategoria", referencedColumnName="id")
     */
    protected $category;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(name="intIDClient", referencedColumnName="id")
     */
    protected $client;

    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    protected $timestamp;

    /**
     * @Column(type="string", name="chrDescripcion", length=250, nullable=true)
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

    public function getFecha() {
        return $this->fecha;
    }

    public function getCreditos() {
        return $this->creditos;
    }

    public function getMonto() {
        return $this->monto;
    }

    public function getClient() {
        return $this->client;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function setCreditos($creditos) {
        $this->creditos = $creditos;
    }

    public function setMonto($monto) {
        $this->monto = $monto;
    }

    public function setClient($client) {
        $this->client = $client;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }
    public function setCategory($category) {
        $this->category = $category;
    }
}
