<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_ComprobanteRepository")
 * @Table(name="tblcomprobante", uniqueConstraints={@UniqueConstraint(name="search_idx", columns={"chrNombreImg"})})
 */
class DefaultDb_Entities_Comprobante {

    /**
     * @Id @GeneratedValue @Column(type="integer",  name="intIDComprobante")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="string",  name="chrNombreImg")
     * @var string
     */
    protected $nombreImg;

    /**
     * @Column(type="string",  name="chrPath")
     * @var string
     */
    protected $path;

    /**
     * @Column(type="string",  name="chrTipoArchivo")
     * @var string
     */
    protected $tipoArchivo;

    /**
     * @Column(type="date",  name="dtdFecha")
     * @var DateTime
     */
    protected $fecha;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $client;

    /**
     * @Column(type="datetime", name="dtdTimeStamp")
     * @var datetime
     */
    protected $timestamp;

    public function getId() {
        return $this->id;
    }

    public function getNombreImg() {
        return $this->nombreImg;
    }

    public function getPath() {
        return $this->path;
    }

    public function getTipoArchivo() {
        return $this->tipoArchivo;
    }

    public function getFecha() {
        return $this->fecha;
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

    public function setNombreImg($nombreImg) {
        $this->nombreImg = $nombreImg;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function setTipoArchivo($tipoArchivo) {
        $this->tipoArchivo = $tipoArchivo;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function setClient($client) {
        $this->client = $client;
    }

    public function setTimestamp($usuario) {
        $this->timestamp = $usuario;
    }

}
