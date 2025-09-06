<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_SaldoMovimientoRepository")
 * @Table(name="tblsaldomovimiento")
 */
class DefaultDb_Entities_SaldoMovimiento {

    /**
     * @Id @GeneratedValue @Column(type="integer",  name="intIDSaldoMov")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="datetime",  name="dtdFecha")
     * @var datetime
     */
    protected $fecha;

    /**
     * @Column(type="string",  name="chrConcepto")
     * @var string
     */
    protected $concepto;

    /**
     * @Column(type="string",  name="chrReferencia")
     * @var string
     */
    protected $referencia;

    /**
     * @Column(type="string",  name="chrTipoMoneda")
     * @var string
     */
    private $tipoMoneda;

    /**
     * @Column(type="string",  name="chrTipoMovimiento")
     * @var string
     */
    private $tipoMovimiento;

   /**
     * @Column(type="string",  name="chrTipoPagos")
     * @var string
     */
    private $tipoPagos;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * */
    private $user;

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

    public function getFecha() {
        return $this->fecha;
    }

    public function getConcepto() {
        return $this->concepto;
    }

    public function getReferencia() {
        return $this->referencia;
    }

    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }

    public function getTipoMovimiento() {
        return $this->tipoMovimiento;
    }

    public function getTipoPagos() {
        return $this->tipoPagos;
    }

    public function getUser() {
        return $this->user;
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

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function setConcepto($concepto) {
        $this->concepto = $concepto;
    }

    public function setReferencia($referencia) {
        $this->referencia = $referencia;
    }

    public function setTipoMoneda($tipoMoneda) {
        $this->tipoMoneda = $tipoMoneda;
    }

    public function setTipoMovimiento($tipoMovimiento) {
        $this->tipoMovimiento = $tipoMovimiento;
    }

    public function setTipoPagos($tipoPagos) {
        $this->tipoPagos = $tipoPagos;
    }

    public function aetUser($user) {
        $this->user = $user;
    }

    public function setClient($client) {
        $this->client = $client;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

}
